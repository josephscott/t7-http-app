<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'home', function () {
	$response = $this->http->get( url: 'http://127.0.0.1:31313/' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
} );

test( 'json', function () {
	$response = $this->http->get( url: 'http://127.0.0.1:31313/json/' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'application/json' );

} );
