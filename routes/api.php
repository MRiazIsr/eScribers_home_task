<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::middleware('auth:api')->post('logout', 'AuthController@logout');

Route::group(['prefix' => 'products', 'middleware' => 'auth'], function () {
    Route::get('/', 'ProductController@index');
    Route::get('/{id}', 'ProductController@getById');
    Route::patch('/{id}', 'ProductController@update');
    Route::delete('/{id}', 'ProductController@destroy');
});

Route::group(['prefix' => 'orders', 'middleware' => 'auth'], function () {
    Route::get('/', 'OrderController@index');
    Route::get('/{id}', 'OrderController@getById');
    Route::post('/{id}', 'OrderController@create');
    Route::patch('/{id}', 'OrderController@update');
});
