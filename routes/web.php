<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/addDepartment', [
    'middleware' => ['Admin'],
    'uses' => 'Admin@addDepartment'
]);

$router->post('/addDivision', [
    'middleware' => ['Admin'],
    'uses' => 'Admin@addDivision'
]);

$router->post('/addShift', [
    'middleware' => ['Admin'],
    'uses' => 'Admin@addShift'
]);

$router->post('/addDoctor', [
    'middleware' => ['Admin'],
    'uses' => 'Admin@addDoctor'
]);

