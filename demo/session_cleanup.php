<?php
declare( strict_types = 1 );

// $app is provided when the route handler is a plain PHP file
$app->response->withHeader( 'Content-Type', 'application/json' );

// Get the session object
$session = $app->request->session();

// Get the session ID before destroying
$session_id = $session->getId();

// Clear all session data
$session->flush();

$response = [
	'success' => true,
	'message' => 'Session destroyed',
	'session_id' => $session_id,
];

echo json_encode( $response, JSON_PRETTY_PRINT );
