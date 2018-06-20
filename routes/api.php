<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/load_component/{name?}', 'ComponentController@loadComponent')->name("LoadComponent");
Route::get('/load_components', 'ComponentController@loadComponents')->name("LoadComponents");
Route::post('/save_component', 'ComponentController@saveComponent')->name("SaveComponent");
