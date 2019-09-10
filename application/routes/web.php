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

$router->group(['prefix' => 'auth'], function () use ($router) {
    /* Auth */
    $router->post('/login', 'AuthController@index');
    $router->post('/logout', 'AuthController@logout');
});

$router->group(['prefix' => 'results', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'ResultsController@index');
});

$router->group(['prefix' => 'manufacturer', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'ManufacturerController@index');
});

$router->group(['prefix' => 'asset_type', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'AssetTypeController@index');
});

$router->group(['prefix' => 'roles', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'RolesController@index');
});

$router->group(['prefix' => 'stations', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'StationsController@index');
});

$router->group(['prefix' => 'seqscheme', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'SeqSchemeController@index');
});

$router->group(['prefix' => 'seqschemegroup', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'SeqSchemeGroupController@index');
});
