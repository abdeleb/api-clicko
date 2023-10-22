<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiController;
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

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'user'], function () {
    Route::get('/', [ApiController::class, 'users']);
    Route::get('/top-domains', [ApiController::class, 'topDomains']);
    Route::post('/create', [ApiController::class, 'createUser']);
    Route::get('/{id}', [ApiController::class, 'showUser']);
    Route::post('/edit/{id}', [ApiController::class, 'updateUser']);
    Route::delete('/{id}', [ApiController::class, 'deleteUser']);

    Route::get('/logout', [ApiAuthController::class, 'logout']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/logout', [ApiAuthController::class, 'logout']);
});

Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);
