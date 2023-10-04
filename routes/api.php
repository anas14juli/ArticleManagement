<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\RegisterController;
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
Route::post('signIn', [RegisterController::class, 'signIn']);
Route::post('signUp', [RegisterController::class, 'signUp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('Articles', ArticleController::class, [
        'except' => ['edit', 'create']
    ]);

    Route::get('PublishedArticles', [ArticleController::class, 'getPublishedArticles']);
    Route::post('updateArticle/{id}', [ArticleController::class, 'updateArticle']);
    Route::post('acceptArticle/{id}', [ArticleController::class, 'acceptArticle']);
    Route::post('search', [ArticleController::class, 'search']);
    Route::post('addComment/{id}', [ArticleController::class, 'addComment']);
    Route::get('getCommentsForAnArticle/{id}', [ArticleController::class, 'getCommentsForAnArticle']);



});
