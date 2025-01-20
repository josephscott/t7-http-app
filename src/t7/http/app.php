<?php
declare( strict_types = 1 );

namespace T7\HTTP;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Workerman\Worker;
use function FastRoute\simpleDispatcher;
use function error_log;
use function is_readable;

class App {
	public string $origin = 'http://127.0.0.1:31313';

	private object $router;

	private string $routes_file;

	private object $worker;

	public function __construct( string $routes_file ) {
		if ( ! is_readable( $routes_file ) ) {
			$msg = "T7\HTTP\App: routes file is not readable: $routes_file";
			error_log( $msg );
			echo $msg . "\n";
			exit( 1 );
		}

		$this->routes_file = $routes_file;
	}

	public function load_routes(): void {
		$this->router = simpleDispatcher( function ( RouteCollector $router ) {
			require $this->routes_file;
		} );
	}

	public function on_worker_start( Worker $worker ) : void {
		$this->load_routes();
	}

	public function run() {
		$this->worker = new Worker( $this->origin );
		$this->worker->onWorkerStart = [ $this, 'on_worker_start' ];
	}
}
