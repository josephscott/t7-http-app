<?php
declare( strict_types = 1 );

// $router is made available by the App class load_routes method

$router->map( 'GET', '/', __DIR__ . '/home.php' );
$router->map( 'GET', '/dump/', __DIR__ . '/dump.php' );
$router->map( 'GET', '/json/', __DIR__ . '/json.php' );

$router->map( 'POST', '/form/', __DIR__ . '/form.php' );
