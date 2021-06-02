<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;


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

Route::group(['middleware' => ['auth']], function () {
    Route::middleware('saml')->group(function () {
        Route::get('/{any}', function () {
            return view('main');
        })
        ->name('dashboard')
        ->where('any', '.*');
    });
});

Route::post('upload', \App\Http\Controllers\UploadController::class . '@handle');

