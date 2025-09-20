<?php
declare( strict_types = 1 );

require __DIR__ . '/../vendor/autoload.php';

$app = new \T7\HTTP\App(
	routes_file: __DIR__ . '/routes.php',
	routes_path: __DIR__ . '/routes'
);
$app->server_name = 't7-demo';
$app->route_404 = __DIR__ . '/404.php';
$app->worker_count = 4;

$app->run();
