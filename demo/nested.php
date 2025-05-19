<?php
declare( strict_types = 1 );

// $app is provided when the route handler is a plain PHP file
$app->response->Header( 'Content-Type', 'application/json' );

$data = [
	'nested' => true,
	'path' => '/nested/route/path/',
];

echo json_encode( $data, JSON_PRETTY_PRINT );
