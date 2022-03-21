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
    $router->put('/group/{G_ID}/admin', 'GroupController@updateAdmin');
    $router->delete('/group/{G_ID}', 'GroupController@delete');
    $router->post('/group/{G_ID}/members', 'GroupController@join');
    $router->delete('/group/{G_ID}/members', 'GroupController@leave');

    $router->get('/receipt', 'ReceiptController@get');
    $router->post('/receipt', 'ReceiptController@createReceipt');

    $router->get('/receipt/steps', 'ReceiptController@getSteps');

    $router->get('/receipt/ingredient', 'ReceiptController@getIngredients');
    $router->get('/receipt/ingredient/names', 'ReceiptController@getIngredientNames');

    $router->get('/receipt/unit', 'ReceiptController@getUnitNames');
    $router->get('/unit', 'ReceiptController@getAllUnits');

    /* $router->get('/files/{path}', 'FilesController@get');
    $router->post('/files', 'FilesController@upload'); */
    $router->post('receipt/image', 'FilesController@uploadReceiptImages');
    $router->get('receipt/image', 'FilesController@getReceiptImages');

    $router->get('/tag', 'ReceiptController@getAllTags');
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

