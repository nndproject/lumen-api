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
    // return view('index');
    return $router->app->version();
});

Route::group([
    'prefix' => 'api'
], function ($router) {

    Route::group(['middleware' => 'throttle:3, 1'], function(){
        Route::post('login', 'AuthController@login');
    });

    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('profile', 'AuthController@me');

    Route::group(['middleware' => 'auth:api'], function(){

        Route::group(['prefix' => 'master-certificate'], function() {
            Route::get('/', 'MasterCertificateController@index');
            Route::post('/', 'MasterCertificateController@store');
            Route::get('/{id}', 'MasterCertificateController@show');
            Route::delete('/{id}', 'MasterCertificateController@destroy');
            Route::post('/{id}', 'MasterCertificateController@update');
            Route::get('/search/{param}', 'MasterCertificateController@search');
        });

    });

});
