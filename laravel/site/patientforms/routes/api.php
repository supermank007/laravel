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

$api = app('Dingo\Api\Routing\Router');

$api->version('v0.1.0', function ($api) {

    $api->group(['middleware' => ['api.auth']], function($api) {

        $api->group(['middleware' => 'role:Super-Admin,Master-Admin'], function() use ($api) {

            $api->resource('userregistration', 'App\Http\Controllers\UserRegistrationController', ['only' => [
                'store'
            ]]);

            $api->get('userresponses', 'App\Http\Controllers\FormUserResponseController@api_fetchResponseData');
            $api->get('userresponses/{registration_number}', 'App\Http\Controllers\FormUserResponseController@api_fetchRegistrationResponseData');

            $api->put('userregistrationdischargedate/{registration_number}', 'App\Http\Controllers\UserRegistrationController@api_dischargeDate');

        });

    });

    $api->resource('authenticate', 'App\Http\Controllers\AuthenticateController', ['only' => ['index']]);
    $api->post('authenticate', 'App\Http\Controllers\AuthenticateController@authenticate');

});