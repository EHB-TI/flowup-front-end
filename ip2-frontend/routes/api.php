<?php


use App\Http\Controllers\StatusController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventSubscriberController;
use App\Http\Controllers\UserController;
use App\Models\EventSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('api')->group(function () {
    Route::resource('events', EventController::class);
    Route::resource('users', UserController::class);

});

Route::post('checkName', \App\Http\Controllers\EventController::class . '@checkName');
Route::post('checkDescription', \App\Http\Controllers\EventController::class . '@checkDescription');
Route::post('checkLocation', \App\Http\Controllers\EventController::class . '@checkLocation');
Route::post('checkDate', \App\Http\Controllers\EventController::class . '@checkDate');


Route::post('participate', \App\Http\Controllers\EventSubscriberController::class . '@store');


Route::get('showByUser/{id}', \App\Http\Controllers\EventController::class . '@showByUser');
Route::get('showSubscribers/{id}', \App\Http\Controllers\EventSubscriberController::class . '@show');




Route::get('/status', [StatusController::class, 'GetMssg']);


