<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\FetchArticleController;
use App\Http\Middleware\VerifyToken;
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

Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login'])->name('login');
Route::post('logout', [AuthController::class,'logout']);
Route::middleware('auth:api')->get('user', [AuthController::class,'getAuthenticatedUser']);

//Route::post('fetch', [FetchArticleController::class,'fetch']);
//Route::get('articles', [FetchArticleController::class,'index']);
//Route::get('/customizeInfo', [FetchArticleController::class, 'customizeInfo']);
//Route::get('/search', [FetchArticleController::class, 'search']);
//
//
//Route::get('/checkSetting', [FetchArticleController::class, 'checkSetting']);
//Route::post('/storeSetting', [FetchArticleController::class, 'storeSetting']);
Route::group(['middleware' => ['jwt.auth']], function () {
    Route::post('fetch', [FetchArticleController::class,'fetch']);
    Route::get('articles', [FetchArticleController::class,'index']);
    Route::get('/customizeInfo', [FetchArticleController::class, 'customizeInfo']);
    Route::get('/search', [FetchArticleController::class, 'search']);

    Route::get('/checkSetting', [FetchArticleController::class, 'checkSetting']);
    Route::post('/storeSetting', [FetchArticleController::class, 'storeSetting']);
});
