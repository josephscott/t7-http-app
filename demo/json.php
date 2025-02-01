<?php
declare( strict_types = 1 );

// $app is provided when the route handler is a plain PHP file

$app->response->Header( 'Content-Type', 'application/json' );

$data = [
	'json' => true,
];

$data = json_encode( $data, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT );
echo $data;
