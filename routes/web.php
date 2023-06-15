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

// 使用者登入 POST 參數: account, password
$router->post('/userLogin', 'User@userLogin');
// 使用者註冊 POST 參數: account, password, name, phone ( name, phone 可以為空 )
$router->post('/newUser', 'User@newUser');
// 櫃台登入 POST 參數: account, password
$router->post('/staffLogin', 'Staff@staffLogin');
// 管理員登入 POST 參數: account, password
$router->post('/adminLogin', 'Admin@adminLogin');
// 取得所有科別 GET 參數: 無
$router->get('/getAllDivision','User@getAllDivision');
// 取得班表 GET 參數: divisionID
$router->get('/getShift','User@getShift');

// 管理員新增大科別 POST 參數: departmentName
$router->post('/addDepartment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@addDepartment'
]);

// 管理員新增小科別 POST 參數: divisionName, departmentName
$router->post('/addDivision', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@addDivision'
]);

// 管理員新增班別 POST 參數: doctorIDNumber divisionName date timePeriod
$router->post('/addShift', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@addShift'
]);

// 管理員新增醫生 POST 參數: doctorIDNumber doctorName 
$router->post('/addDoctor', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@addDoctor'
]);

// 使用者新增掛號 POST 參數: shiftID
$router->post('/userAddAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userAddAppointment'
]);

// 櫃台新增掛號 POST 參數: shiftID, IdNumber
$router->post('/staffAddAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Staff@staffAddAppointment'
]);

// 櫃台新增使用者 POST 參數: account, password, name, phoneNumber ( name, phoneNumber 可以為空 )
$router->post('/addUser', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Staff@addUser'
]);

// 使用者查詢自己有效的掛號 GET 參數: 無
$router->get('/userGetValidAppointmentUserID', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userGetValidAppointmentUserID'
]);

// 使用者取消掛號 PATCH 參數: shiftID
$router->patch('/userCancelAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userCancelAppointment'
]);

// 使用者取得使用者資訊 GET 參數: 無
$router->get('/userGetUser', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userGetUser'
]);

// 使用者更新使用者資訊 PATCH 參數: account, password, name, phoneNumber ( name, phoneNumber 可以為空 )
$router->patch('/userUpdateUser', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userUpdateUser'
]);
