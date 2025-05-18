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
