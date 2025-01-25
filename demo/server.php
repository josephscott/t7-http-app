<?php
declare( strict_types = 1 );

require __DIR__ . '/../vendor/autoload.php';

$app = new \T7\HTTP\App(
	routes_file: __DIR__ . '/routes.php'
);

$app->run();
