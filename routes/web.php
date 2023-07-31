<?php

// use Illuminate\Support\Facades\Route;

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
    return '#nndproject';
    return $router->app->version();
});

Route::group([
    'prefix' => 'api'
], function ($router) {

   /*  Route::group(['middleware' => 'throttle:2,1'], function(){
       
    }); */

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('user-profile', 'AuthController@me');

    Route::group(['middleware' => 'auth:api'], function(){
        Route::get('warranty/{id}', 'WarrantyController@checkWarranty');
    });


});