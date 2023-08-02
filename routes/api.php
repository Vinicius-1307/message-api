<?php

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

Route::group(['middleware' => 'jwt.auth'], function () {
});
Route::prefix('user')->group(function () {
    Route::post('/', [\App\Http\Controllers\UserController::class, 'create']);
    Route::post('/login', [\App\Http\Controllers\UserController::class, 'login']);
});

Route::middleware('auth:api')->get('/token', function (Request $request) {
    return $request->user();
});
//User routes

//Messages routes
Route::prefix('messages')->group(function () {
    Route::post('/', [\App\Http\Controllers\Messages\MessagesController::class, 'send']);
    Route::get('/', [\App\Http\Controllers\Messages\MessagesController::class, 'list']);
    Route::patch('/{id}', [\App\Http\Controllers\Messages\MessagesController::class, 'read']);
    Route::delete('/{id}', [\App\Http\Controllers\Messages\MessagesController::class, 'delete']);
});
