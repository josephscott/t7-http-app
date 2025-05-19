<?php
declare( strict_types = 1 );

// $app is provided when the route handler is a plain PHP file
$app->response->withHeader( 'Content-Type', 'application/json' );

// Get the session object from the request
$session = $app->request->session();

// Get data from POST request
$post_data = $app->request->post();

// Store in session
foreach ( $post_data as $key => $value ) {
	$session->set( $key, $value );
}

$response = [
	'success' => true,
	'session_id' => $session->getId(),
	'message' => 'Session data stored',
	'stored_keys' => array_keys( $post_data ),
];

echo json_encode( $response, JSON_PRETTY_PRINT );
