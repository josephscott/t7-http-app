<?php
declare( strict_types = 1 );

beforeEach( function () {
	$this->http = new \T7\HTTP\Request();
} );

test( 'home', function () {
	$response = $this->http->get( url: 'http://127.0.0.1:31313/' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'text-plain' );
} );

test( 'json', function () {
	$response = $this->http->get( url: 'http://127.0.0.1:31313/json/' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'application/json' );

	$data = json_decode( $response->body, true );
	expect( $data )->toHaveKey( 'json' );
	expect( $data['json'] )->toBeTrue();
} );

test( '404 for non-existent route', function () {
	$response = $this->http->get( url: 'http://127.0.0.1:31313/not-found/' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 404 );
	expect( $response->body )->toContain( 'Lost?' );
} );

test( '302 redirect for missing trailing slash', function () {
	$response = $this->http->get( url: 'http://127.0.0.1:31313/json' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 302 );
	expect( $response->headers['location'] )->toBe( '/json/' );
} );

test( 'dump endpoint with query parameters', function () {
	$response = $this->http->get( url: 'http://127.0.0.1:31313/dump/?test=value' );

	expect( $response->error )->toBe( false );
	expect( $response->code )->toBe( 200 );
	expect( $response->body )->toContain( 'Headers' );
	expect( $response->body )->toContain( 'Method' );
	expect( $response->body )->toContain( 'test=value' );
} );
