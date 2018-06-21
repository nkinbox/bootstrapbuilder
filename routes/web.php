<?php
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/component/create','ComponentController@create')->name("CreateComponent");
Route::get('/component/edit/{name}','ComponentController@edit')->name("EditComponent");
Route::get('/component/add_basic','ComponentController@addBasic')->name("AddBasicComponent");
Route::post('/component/add','ComponentController@add')->name("AddComponent");


Route::get('/home', 'HomeController@index')->name('home');
