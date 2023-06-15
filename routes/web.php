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
$router->get('/getAllDivision','User@getAllDivision');
$router->get('/getShift','User@getShift');


$router->post('/addDepartment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@addDepartment'
]);

$router->post('/addDivision', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@addDivision'
]);

$router->post('/addShift', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@addShift'
]);

$router->post('/addDoctor', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@addDoctor'
]);

$router->post('/userAddAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userAddAppointment'
]);

$router->post('/staffAddAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Staff@staffAddAppointment'
]);

$router->post('/addUser', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Staff@addUser'
]);

$router->get('/userGetValidAppointmentUserID', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userGetValidAppointmentUserID'
]);

$router->patch('/userCancelAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userCancelAppointment'
]);

$router->get('/userGetUser', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userGetUser'
]);

$router->patch('/userUpdateUser', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userUpdateUser'
]);
