<?php
declare( strict_types = 1 );

// $app is provided when the route handler is a plain PHP file
$app->response->withHeader( 'Content-Type', 'application/json' );

$data = [
	'text' => $app->vars['text'],
	'received' => true,
];

echo json_encode( $data, JSON_PRETTY_PRINT );
