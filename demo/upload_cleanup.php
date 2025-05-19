<?php
declare( strict_types = 1 );

// $app is provided when the route handler is a plain PHP file
$app->response->withHeader( 'Content-Type', 'application/json' );

$post_data = $app->request->post();
$deleted_files = [];
$failed_files = [];

// Check if paths were provided
if ( isset( $post_data['paths'] ) && is_array( $post_data['paths'] ) ) {
	foreach ( $post_data['paths'] as $path ) {
		// Security check - only allow deletion within the upload directory
		// This is important for production systems
		$upload_dir = sys_get_temp_dir(); // Or your configured upload directory
		$real_path = realpath( $path );

		if ( $real_path && file_exists( $real_path ) && strpos( $real_path, $upload_dir ) === 0 ) {
			if ( unlink( $real_path ) ) {
				$deleted_files[] = $path;
			} else {
				$failed_files[] = $path;
			}
		} else {
			$failed_files[] = $path;
		}
	}
}

$response = [
	'success' => empty( $failed_files ),
	'message' => empty( $failed_files )
		? 'All files deleted successfully'
		: 'Some files could not be deleted',
	'deleted' => $deleted_files,
	'failed' => $failed_files,
];

echo json_encode( $response, JSON_PRETTY_PRINT );
