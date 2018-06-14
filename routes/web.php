<?php
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/component/create','ComponentController@index')->name("CreateComponent");
Route::get('/component/add_basic','ComponentController@addBasic')->name("AddBasicComponent");
Route::post('/component/add','ComponentController@add')->name("AddComponent");


Route::get('/home', 'HomeController@index')->name('home');
