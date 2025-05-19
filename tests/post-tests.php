<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'form submission', function () {
	$form_data = [
		'name' => 'Test User',
		'email' => 'test@example.com',
		'message' => 'Hello, world!',
	];

	$response = $this->http->post(
		url: 'http://127.0.0.1:31313/form/',
		data: $form_data
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'application/json' );

	$data = json_decode( $response->body, true );
	expect( $data )->toHaveKey( 'success' );
	expect( $data['success'] )->toBeTrue();
	expect( $data )->toHaveKey( 'data' );
	expect( $data['data'] )->toMatchArray( $form_data );
} );

test( 'session management', function () {
	// 1. Set session data
	$session_data = [
		'user_id' => 12345,
		'username' => 'test_user',
		'is_admin' => false,
	];

	$response = $this->http->post(
		url: 'http://127.0.0.1:31313/session/',
		data: $session_data
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );

	$data = json_decode( $response->body, true );
	expect( $data['success'] )->toBeTrue();
	expect( $data )->toHaveKey( 'session_id' );

	// Get session ID from the response body since we can't access cookies
	$session_id = $data['session_id'];
	expect( $session_id )->not->toBeEmpty();

	// 2. Read session data by passing the session ID in a cookie header
	$cookie_header = ['cookie' => 'PHPSID=' . $session_id];
	$response = $this->http->get(
		url: 'http://127.0.0.1:31313/session/',
		headers: $cookie_header
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );

	$data = json_decode( $response->body, true );
	expect( $data )->toHaveKey( 'session_data' );
	expect( $data['session_data'] )->toHaveKey( 'user_id' );
	expect( $data['session_data'] )->toMatchArray( $session_data );

	// 3. Clean up the session - destroy session data
	$cleanup_data = ['action' => 'cleanup'];
	$response = $this->http->post(
		url: 'http://127.0.0.1:31313/session/cleanup/',
		headers: $cookie_header,
		data: $cleanup_data
	);

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );

	// Verify session is gone
	$response = $this->http->get(
		url: 'http://127.0.0.1:31313/session/',
		headers: $cookie_header
	);

	$data = json_decode( $response->body, true );
	expect( $data['session_data'] )->toBeEmpty();
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
