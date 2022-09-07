<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::get('/test',function(){
//     return "ok"; 
//});

Route::get('chargebee/cancel/{key}', ['uses' => 'ChargebeeController@subscriptionCancel'])->name('chargebee-subscription-cancel');
Route::post('chargebee/cancel/{key}', ['uses' => 'ChargebeeController@subscriptionCancel']);

Route::get('chargebee/renew/{key}', ['uses' => 'ChargebeeController@subscriptionRenew'])->name('chargebee-subscription-renew');
Route::post('chargebee/renew/{key}', ['uses' => 'ChargebeeController@subscriptionRenew']);


