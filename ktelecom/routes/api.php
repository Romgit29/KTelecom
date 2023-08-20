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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'App\Http\Controllers'], function()
{
    Route::group(['prefix' => 'equipment'], function() {
        Route::post('', 'EquipmentController@store');
        Route::put('/{id}', 'EquipmentController@update'); 
        Route::delete('/{id}', 'EquipmentController@delete'); 
        Route::get('', 'EquipmentController@getEquipment'); 
    });

    Route::get('/equipment-type', 'EquipmentController@getEquipmentTypes');
});
