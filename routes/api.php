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

Route::group(['prefix' => 'v1', 'middleware' => 'web'], function (){
    Route::get(
        '/user/me',
        [\App\Http\Controllers\API\V1\PDFController::class, 'getUser']
    );

    Route::post(
        '/reading/update',
        [\App\Http\Controllers\API\V1\PDFController::class, 'updateLastPageData']
    );
});
