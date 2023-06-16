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

/** 
 * 以下為病患相關 API
 */

// 病患登入 POST 參數: account, password
$router->post('/userLogin', 'User@userLogin');
// 病患註冊 POST 參數: account, password, name, phone ( name, phone 可以為空 )
$router->post('/newUser', 'User@newUser');
// 取得所有科別 GET 參數: 無
$router->get('/getAllDivision', 'User@getAllDivision');
// 取得班表 GET 參數: divisionID
$router->get('/getShift', 'User@getShift');


// 病患新增掛號 POST 參數: shiftID
$router->post('/userAddAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userAddAppointment'
]);
// 病患查詢自己有效的掛號 GET 參數: 無
$router->get('/userGetValidAppointmentUserID', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userGetValidAppointmentUserID'
]);
// 病患取消掛號 PATCH 參數: shiftID
$router->patch('/userCancelAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userCancelAppointment'
]);
// 病患取得病患資訊 GET 參數: 無
$router->get('/userGetUser', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userGetUser'
]);
// 病患修改病患資訊 PATCH 參數:  name, phoneNumber ( name, phoneNumber 可以為空 )
$router->patch('/userUpdateUser', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'User@userUpdateUser'
]);




/** 
 * 以下為櫃台相關 API
 */

// 櫃台登入 POST 參數: account, password
$router->post('/staffLogin', 'Staff@staffLogin');


// 櫃台取得病患資訊 POST 參數: account
$router->get('/staffGetUserAccount', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Staff@staffGetUserAccount'
]);
// 櫃台新增掛號 POST 參數: shiftID, IdNumber
$router->post('/staffAddAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Staff@staffAddAppointment'
]);
// 櫃台新增病患 POST 參數: account, password, name, phoneNumber ( name, phoneNumber 可以為空 )
$router->post('/addUser', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Staff@addUser'
]);
// 櫃台查詢病患有效的掛號 GET 參數: account
$router->get('/staffGetUserValidAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Staff@staffGetUserValidAppointment'
]);
// 櫃台取消使掛號 PATCH 參數: account, shiftID
$router->patch('/staffCancelAppointment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Staff@staffCancelAppointment'
]);
// 櫃台修改病患資訊 PATCH 參數: account, name, phoneNumber ( name, phoneNumber 可以為空 )
$router->patch('/staffUpdateUser', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Staff@staffUpdateUser'
]);


/** 
 * 以下為管理員相關 API
 */

// 管理員登入 POST 參數: account, password
$router->post('/adminLogin', 'Admin@adminLogin');


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
// 管理員查詢所有大科別 GET 參數: 無
$router->get('/adminGetAllDepartment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminGetAllDepartment'
]);
// 管理員查詢所有小科別 GET 參數: DepartmentName
$router->get('/adminGetAllDivision', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminGetAllDivision'
]);
// 管理員查詢所有醫生 GET 參數: 無
$router->get('/adminGetAllDoctor', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminGetAllDoctor'
]);
// 管理員依據小科別查詢所有班別 GET 參數: divisionName
$router->get('/adminGetAllShift', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminGetAllShift'
]);
// 管理員修改大科別 PATCH 參數: departmentID, departmentName
$router->patch('/adminUpdateDepartment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminUpdateDepartment'
]);
// 管理員修改小科別 PATCH 參數: divisionID, divisionName, departmentName
$router->patch('/adminUpdateDivision', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminUpdateDivision'
]);
// 管理員修改醫生 PATCH 參數: doctorID, doctorName, doctorIDNumber
// (doctorName, doctorIDNumber可以為空)
$router->patch('/adminUpdateDoctor', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminUpdateDoctor'
]);
// 管理員修改班別 PATCH 參數: shiftID, doctorIDNumber, divisionName, date, timePeriod 
// (doctorIDNumber, divisionName, date, timePeriod可以為空)
$router->patch('/adminUpdateShift', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminUpdateShift'
]);
// 管理員刪除大科別 DELETE 參數: departmentName
$router->delete('/adminDeleteDepartment', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminDeleteDepartment'
]);
// 管理員刪除小科別 DELETE 參數: divisionName
$router->delete('/adminDeleteDivision', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminDeleteDivision'
]);
// 管理員刪除醫生 DELETE 參數: doctorIDNumber
$router->delete('/adminDeleteDoctor', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminDeleteDoctor'
]);
// 管理員刪除班別 DELETE 參數: shiftID
$router->delete('/adminDeleteShift', [
    'middleware' => ['RolePermission', 'LoginAuthenticate'],
    'uses' => 'Admin@adminDeleteShift'
]);