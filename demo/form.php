<?php
declare( strict_types = 1 );

// $app is provided when the route handler is a plain PHP file

$app->response->Header( 'Content-Type', 'application/json' );

$post_data = $app->request->post();
$response = [
	'success' => true,
	'message' => 'Form data received',
	'data' => $post_data,
];

echo json_encode( $response, JSON_PRETTY_PRINT );
