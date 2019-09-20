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

use Illuminate\Http\Request;


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

$router->group(['prefix' => 'report_type', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'ReportTypeController@index');
});

$router->group(['prefix' => 'roles', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'RolesController@index');
    $router->get('/get_users[/{user_id}]', 'RolesController@getUsers');
});

$router->group(['prefix' => 'stations', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'StationsController@index');
});

$router->group(['prefix' => 'seqscheme', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'SeqSchemeController@index');
    $router->get('/see_the_flow', 'SeqSchemeController@seeTheFlow');
});

$router->group(['prefix' => 'seqschemegroup', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'SeqSchemeGroupController@index');
});

$router->group(['prefix' => 'users', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'UsersController@index');
    $router->patch('/change_password', 'UsersController@changePassword');
});

$router->group(['prefix' => 'transactions', 'middleware' => 'eauth'], function () use ($router) {
    $router->get('/', 'TransactionsController@index');
    $router->put('/create', 'TransactionsController@createTransaction');
    $router->post('/save', 'TransactionsController@store');
    $router->get('/current_status/{id_asset}', 'TransactionsController@currentStatus');
    $router->post('/generate_result', 'TransactionsController@generateResult');
    $router->get('/list_transaction', 'TransactionsController@listTransactionAsset');
});

$router->group(['prefix' => 'assets', 'middleware' => 'eauth'], function () use ($router) {
    $router->post('/', 'AssetsController@index');
    $router->post('/save', 'AssetsController@store');
    $router->get('/detail', 'AssetsController@detail');
    $router->delete('/delete/{asset_id}', 'AssetsController@delete');
    $router->delete('/delete_all', 'AssetsController@deleteAll');
    $router->get('/test_detail[/{asset_id}]', 'AssetsController@testDetail');
});

$router->group(['prefix' => 'stock_movement', 'middleware' => 'eauth'], function () use ($router) {
    $router->post('/', 'StockMovementController@index');
    $router->put('/save', 'StockMovementController@store');
    $router->delete('/delete', 'StockMovementController@delete');
    $router->delete('/delete_asset/{stock_movement}', 'StockMovementController@deleteAsset');
    $router->put('/save_asset', 'StockMovementController@storeAssets');
    $router->delete('/delete_all', 'StockMovementController@deleteAll');
    $router->get('/list_stock_asset', 'StockMovementController@listStockAsset');
    $router->get('/detail', 'StockMovementController@show');
    $router->patch('/accept', 'StockMovementController@accept');
    $router->patch('/approve_gr', 'StockMovementController@approveGR');
    $router->get('/get_ready_assets', 'StockMovementController@getReadyAssets');
    
    $router->get('/generate_document_number', 'StockMovementController@generateDocumentNumber');
});

$router->get('watch/{nama}/', 'WatchController@default');

