<?php
declare( strict_types = 1 );

// $app is provided when the route handler is a plain PHP file
$app->response->withHeader( 'Content-Type', 'application/json' );

// Get the session object from the request
$session = $app->request->session();

// Return the session data
$response = [
	'session_id' => $session->getId(),
	'session_data' => $session->all(),
];

echo json_encode( $response, JSON_PRETTY_PRINT );
