<?php

use App\Builder\ReturnApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Messages\MessagesController;

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

Route::post('/login', [LoginController::class, 'login'])->name('login');

// AUTHENTICATION
Route::middleware('authJwt')->group(function () {
    Route::middleware('admin')->group(function () {

        //User routes
        Route::prefix('user')->group(function () {
            Route::post('/', [UserController::class, 'create']);
        });

        //Messages routes
        Route::prefix('messages')->group(function () {
            Route::post('/', [MessagesController::class, 'create']);
            Route::get('/', [MessagesController::class, 'list']);
            Route::patch('/{id}', [MessagesController::class, 'read']);
            Route::delete('/{id}', [MessagesController::class, 'delete']);
        });
    });
});