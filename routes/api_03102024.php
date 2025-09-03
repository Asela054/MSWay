<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('v1/AuthenticateUser',['uses' => '\App\Http\Controllers\Api\AuthController@AuthenticateUser', 'as' => 'AuthenticateUser']);

Route::middleware('auth:api')->group(function () {
    Route::post('v1/GetCustomerBranches', ['uses' => '\App\Http\Controllers\Api\MainController@getCustomerBranches', 'as' => 'GetCustomerBranches']);
    Route::post('v1/AttendanceStore', ['uses' => '\App\Http\Controllers\Api\MainController@attendanceStore', 'as' => 'AttendanceStore']);
    Route::post('v1/GetEmployeeInfo', ['uses' => '\App\Http\Controllers\Api\MainController@getEmployeeInfo', 'as' => 'GetEmployeeInfo']);
    Route::post('v1/EmpLocationStore', ['uses' => '\App\Http\Controllers\Api\MainController@empLocationStore', 'as' => 'EmpLocationStore']);
    Route::post('v1/GetLeaveTypes', ['uses' => '\App\Http\Controllers\Api\MainController@GetLeaveTypes', 'as' => 'GetLeaveTypes']);
    Route::post('v1/GetLeavesList', ['uses' => '\App\Http\Controllers\Api\MainController@GetLeavesList', 'as' => 'GetLeavesList']);
    Route::post('v1/GetLeaveBalance', ['uses' => '\App\Http\Controllers\Api\MainController@GetLeaveBalance', 'as' => 'GetLeaveBalance']);
    Route::post('v1/ApplyLeave', ['uses' => '\App\Http\Controllers\Api\MainController@ApplyLeave', 'as' => 'ApplyLeave']);
});
