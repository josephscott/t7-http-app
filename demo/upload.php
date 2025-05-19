<?php
declare( strict_types = 1 );

// $app is provided when the route handler is a plain PHP file
$app->response->withHeader( 'Content-Type', 'application/json' );

// Process file uploads
$files = $app->request->file();
$response = [
	'success' => false,
	'message' => 'No files uploaded',
	'files' => [],
];

if ( ! empty( $files ) ) {
	$response['success'] = true;
	$response['message'] = 'Files received';

	// Process each uploaded file
	foreach ( $files as $field_name => $file_info ) {
		// In a real app, you'd save the file here
		// For our test, we'll just return metadata
		$response['files'][$field_name] = [
			'name' => $file_info['name'],
			'type' => $file_info['type'],
			'size' => $file_info['size'],
			'tmp_name' => basename( $file_info['tmp_name'] ),
			'error' => $file_info['error'],
			'server_path' => $file_info['tmp_name'], // Include full path for cleanup
		];
	}
}

echo json_encode( $response, JSON_PRETTY_PRINT );
