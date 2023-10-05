<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', static fn () =>redirect('/admin'));

Route::get(
    'documents/get/{hash}',
    [\App\Http\Controllers\DocumentController::class, 'getDocument']
)
    ->name('document.get')
    ->middleware('auth');
