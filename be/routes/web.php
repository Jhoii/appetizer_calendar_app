<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//use App\Http\Controllers\EventController;

$router->get('/', function () use ($router) {
    return response()->json('Welcome bajoy', 200);
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'event'], function () use ($router) {
        $router->post('/create',['uses' => 'EventController@create']);
        $router->get('/test',['uses' => 'EventController@test']);
    });
});
