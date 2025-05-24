<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'file upload', function () {
	// Create a temporary test file
	$temp_file = tempnam( sys_get_temp_dir(), 'test_upload_' );
	$test_content = 'This is test content for file upload';
	file_put_contents( $temp_file, $test_content );

	try {
		// Create form data with the file
		$form_data = [
			'test_file' => curl_file_create( $temp_file, 'text/plain', 'test-file.txt' ),
		];

		// Send the upload request using standard form data with the file
		$response = $this->http->post(
			url: 'http://127.0.0.1:31313/upload/',
			data: $form_data
		);

		expect( $response->error )->toBe( false );
		expect( $response->code )->toBe( 200 );

		$data = json_decode( $response->body, true );
		expect( $data )->toHaveKey( 'success' );
		expect( $data['success'] )->toBeTrue();
		expect( $data )->toHaveKey( 'files' );
		expect( $data['files'] )->toHaveKey( 'test_file' );
		expect( $data['files']['test_file']['name'] )->toBe( 'test-file.txt' );

		// Clean up any files that might have been saved on the server
		if ( isset( $data['files']['test_file']['server_path'] ) ) {
			$cleanup_response = $this->http->post(
				url: 'http://127.0.0.1:31313/upload/cleanup/',
				data: ['paths' => [$data['files']['test_file']['server_path']]]
			);
		}
	} finally {
		// Always clean up the local temporary file
		if ( file_exists( $temp_file ) ) {
			unlink( $temp_file );
		}
	}
} );
