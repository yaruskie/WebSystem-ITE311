<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');      // Default homepage
$routes->get('home', 'Home::index');   // Allows /index.php/home to work

// You can add more routes here if needed
