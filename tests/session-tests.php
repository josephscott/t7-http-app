<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
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
