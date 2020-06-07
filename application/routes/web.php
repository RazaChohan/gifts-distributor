<?php

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

//Score Controller group
$router->group(['prefix' => 'employee'], function () use ($router) {
    //get user
    $router->post(
        '/gift',
        [
            'uses' => 'EmployeeController@getGift'
        ]
    );
});

