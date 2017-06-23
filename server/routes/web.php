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

Route::get('/', function () {
    return redirect("https://github.com/hteen/scude_chrome_extension");
});

Route::get('/login', "LoginController@index");
Route::get('/incr', "LoginController@incr");
Route::get('/minutes', "LoginController@minutes");