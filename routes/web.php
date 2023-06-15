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

$router->post('/userLogin', 'User@userLogin');
$router->post('/newUser', 'User@newUser');
$router->post('/staffLogin', 'Staff@staffLogin');
$router->post('/adminLogin', 'Admin@adminLogin');


$router->post('/addDepartment', [
    'middleware' => ['RolePermission', 'Authenticate'],
    'uses' => 'Admin@addDepartment'
]);

$router->post('/addDivision', [
    'middleware' => ['RolePermission', 'Authenticate'],
    'uses' => 'Admin@addDivision'
]);

$router->post('/addShift', [
    'middleware' => ['RolePermission', 'Authenticate'],
    'uses' => 'Admin@addShift'
]);

$router->post('/addDoctor', [
    'middleware' => ['RolePermission', 'Authenticate'],
    'uses' => 'Admin@addDoctor'
]);

$router->post('/userAddAppointment', [
    'middleware' => ['RolePermission', 'Authenticate'],
    'uses' => 'User@userAddAppointment'
]);

$router->post('/staffAddAppointment', [
    'middleware' => ['RolePermission', 'Authenticate'],
    'uses' => 'Staff@staffAddAppointment'
]);

$router->post('/addUser', [
    'middleware' => ['RolePermission', 'Authenticate'],
    'uses' => 'Staff@addUser'
]);

$router->get('/userGetValidAppointmentUserID', [
    'middleware' => ['RolePermission', 'Authenticate'],
    'uses' => 'User@userGetValidAppointmentUserID'
]);

$router->patch('/userCancelAppointment', [
    'middleware' => ['RolePermission', 'Authenticate'],
    'uses' => 'User@userCancelAppointment'
]);