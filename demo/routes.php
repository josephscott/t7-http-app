<?php
declare( strict_types = 1 );

$router->map( 'GET', '/', [ 'Another' ] );

// $router is made available by the App class load_routes method

$router->map( 'GET', '/', __DIR__ . '/home.php' );
$router->map( 'GET', '/dump/', __DIR__ . '/dump.php' );
$router->map( 'GET', '/json/', __DIR__ . '/json.php' );

$router->map( 'POST', '/form/', __DIR__ . '/form.php' );

// New routes for testing
$router->map( 'GET', '/users/[i:id]/', __DIR__ . '/user.php' );
$router->map( 'GET', '/nested/route/path/', __DIR__ . '/nested.php' );
$router->map( 'GET', '/special-chars/[*:text]/', __DIR__ . '/special.php' );

// Routes for testing sessions
$router->map( 'GET', '/session/', __DIR__ . '/session_view.php' );
$router->map( 'POST', '/session/', __DIR__ . '/session_set.php' );
$router->map( 'POST', '/session/cleanup/', __DIR__ . '/session_cleanup.php' );

// Route for testing file uploads
$router->map( 'POST', '/upload/', __DIR__ . '/upload.php' );
$router->map( 'POST', '/upload/cleanup/', __DIR__ . '/upload_cleanup.php' );
