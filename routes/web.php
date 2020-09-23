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


Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::post('/test', 'HomeController@store')->name('store');
Route::get('/test', 'HomeController@show')->name('show');

Route::post('/download', 'HomeController@download')->name('download');
