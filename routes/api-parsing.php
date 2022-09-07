<?php


/*
|--------------------------------------------------------------------------
| API Parsing Routes
|--------------------------------------------------------------------------
|
|
|  prefix = api-parse
|
*/

Route::get('form/{key}', ['uses' => 'ParsingController@form'])->name('parse-form');
Route::post('form/save/{key}', ['uses' => 'ParsingController@saveProduct']);
Route::post('login', ['uses' => 'ParsingController@login'])->name('parse-login');
Route::post('set-data/{key}', ['uses' => 'ParsingController@setData']);
