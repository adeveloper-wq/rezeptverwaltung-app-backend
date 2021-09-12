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

$router->group(['prefix'=>'api'], function() use($router){
    $router->get('/items', 'ItemController@all');
    $router->get('/items/{id}', 'ItemController@get');
    $router->get('/group', 'GroupController@index');
    $router->get('/group/{name}', 'GroupController@get');
    $router->post('/group', 'GroupController@create');
    $router->put('/group/{G_ID}', 'GroupController@update');
    $router->delete('/group/{G_ID}', 'GroupController@delete');
    $router->post('/group/join', 'GroupController@join');
    $router->get('/receipt', 'ReceiptController@get');
});

$router->group([
    'prefix' => 'auth'
  ], function ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->post('logout', 'AuthController@logout');
    $router->post('refresh', 'AuthController@refresh');
    $router->get('me', 'AuthController@me');
});

