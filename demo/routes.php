<?php
declare( strict_types = 1 );

// $router is made available by the App class load_routes method

$router->map( 'GET', '/', __DIR__ . '/home.php' );
$router->map( 'GET', '/dump/', __DIR__ . '/dump.php' );
$router->map( 'GET', '/json/', __DIR__ . '/json.php' );

$router->map( 'POST', '/form/', __DIR__ . '/form.php' );

// New routes for testing
$router->map( 'GET', '/users/[i:id]/', __DIR__ . '/user.php' );
$router->map( 'GET', '/nested/route/path/', __DIR__ . '/nested.php' );
$router->map( 'GET', '/special-chars/[*:text]/', __DIR__ . '/special.php' );
