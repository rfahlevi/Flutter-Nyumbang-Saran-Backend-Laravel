<?php

use App\Http\Controllers\API\UserController;
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
//API route for register new user
Route::post('/register', [App\Http\Controllers\API\UserController::class, 'register']);
//API route for login user
Route::post('/login', [App\Http\Controllers\API\UserController::class, 'login']);
//API route for Departemen
Route::apiResource('/departemen', \App\Http\Controllers\API\DepartemenController::class);

//Protecting Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    // Mengambil data user yang sedang login
    Route::get('/profile', [UserController::class, 'fetch']);
    Route::post('/profile', [UserController::class, 'updateProfile']);

    // API route for logout user
    Route::post('/logout', [App\Http\Controllers\API\UserController::class, 'logout']);
   
    // API route from resource
    Route::apiResource('/user', \App\Http\Controllers\API\UserController::class);
    Route::apiResource('/saran', \App\Http\Controllers\API\SaranController::class);
    Route::apiResource('/penilaian', \App\Http\Controllers\API\PenilaianController::class);
});




