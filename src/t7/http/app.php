<?php
declare( strict_types = 1 );

namespace T7\HTTP;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use stdClass;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;
use Workerman\Worker;
use function error_log;
use function FastRoute\simpleDispatcher;
use function is_callable;
use function is_readable;
use function is_string;
use function ob_end_clean;
use function ob_get_contents;
use function ob_start;

class App {
	public string $origin = 'http://127.0.0.1:31313';

	private object $router;

	private string $routes_file;

	private object $worker;

	public function __construct( string $routes_file ) {
		if ( ! is_readable( $routes_file ) ) {
			$msg = "routes file is not readable: $routes_file";
			$this->error_log( $msg );
			echo $msg . "\n";
			exit( 1 );
		}

		$this->routes_file = $routes_file;
	}

	public function call_route(
		string|array|callable $handler,
		array $vars,
		Request $request,
		Response $response
	) : Response {
		$app = new stdClass();
		$app->handler = $handler;
		$app->request = $request;
		$app->response = $response;
		$app->vars = $vars;

		$out = '';

		if ( is_string( $handler ) && is_readable( $handler ) ) {
			$call_file = function ( $app ) {
				ob_start();
				require $app->handler;
				$out = ob_get_contents();
				ob_end_clean();
				return $out;
			};
			$out = $call_file( $app );
		} elseif ( is_callable( $handler ) ) {
			$out = $handler( $app );
		}

		$app->response->withBody( $out );
		return $app->response;
	}

	public function error_log( string $msg ): void {
		$msg = "T7\HTTP\App: $msg";
		error_log( $msg );
	}

	public function load_routes(): void {
		$this->router = simpleDispatcher( function ( RouteCollector $router ) {
			require $this->routes_file;
		} );
	}

	public function on_message(
		TcpConnection $connection,
		Request $request
	) : void {
		$response = new Response( 200, [] );

		$match = $this->router->dispatch(
			$request->method(),
			$request->path()
		);

		switch( $match[0] ) {
			case Dispatcher::FOUND:
				$handler = $match[1];
				$vars = $match[2];
				$response = $this->call_route(
					$handler,
					$vars,
					$request,
					$response
				);
				break;
			case Dispatcher::NOT_FOUND:
				// If there was no trailing slash, redirect to the same URL
				// with a trailing slash
				if (
					$request->path() !== '/'
					&& substr( $request->path(), -1 ) !== '/'
				) {
					$response->withStatus( 302 );
					$response->withHeader( 'Location', $request->path() . '/' );
				}
				break;
			case Dispatcher::METHOD_NOT_ALLOWED:
				$response->withStatus( 405 );
				$this->error_log( 'method not allowed' );
				break;
		}

		$connection->send( $response );
	}

	public function on_worker_start( Worker $worker ) : void {
		$this->load_routes();
	}

	public function run() {
		$this->worker = new Worker( $this->origin );
		$this->worker->onWorkerStart = [ $this, 'on_worker_start' ];
		$this->worker->onMessage = [ $this, 'on_message' ];
		$this->worker->runAll();
	}
}
