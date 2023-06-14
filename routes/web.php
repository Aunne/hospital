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
    'middleware' => ['RolePermission'],
    'uses' => 'Admin@addDepartment'
]);

$router->post('/addDivision', [
    'middleware' => ['RolePermission'],
    'uses' => 'Admin@addDivision'
]);

$router->post('/addShift', [
    'middleware' => ['RolePermission'],
    'uses' => 'Admin@addShift'
]);

$router->post('/addDoctor', [
    'middleware' => ['RolePermission'],
    'uses' => 'Admin@addDoctor'
]);

$router->post('/userAddAppointment', [
    'middleware' => ['RolePermission'],
    'uses' => 'User@userAddAppointment'
]);

$router->post('/staffAddAppointment', [
    'middleware' => ['RolePermission'],
    'uses' => 'Staff@staffAddAppointment'
]);

$router->post('/addUser', [
    'middleware' => ['RolePermission'],
    'uses' => 'Staff@addUser'
]);

$router->get('/userGetValidAppointmentUserID', [
    'middleware' => ['RolePermission'],
    'uses' => 'User@userGetValidAppointmentUserID'
]);

$router->patch('/userCancelAppointment', [
    'middleware' => ['RolePermission'],
    'uses' => 'User@userCancelAppointment'
]);

