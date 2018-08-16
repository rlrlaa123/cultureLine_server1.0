<?php

use Illuminate\Http\Request;

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

Route::post('auth/login', 'AuthController@login');
Route::post('auth/register', 'AuthController@register');
Route::post('auth/logout', 'AuthController@logout');
Route::get('auth/me', 'AuthController@me');

Route::get('category', 'API\CategoryController@index');

Route::resource('qna', 'API\QNAController');

Route::prefix('question/{question}')->group(function() {
    Route::resource('answer', 'API\AnswerController');
    Route::post('answer/{answer}/like', 'API\AnswerController@like')->name('answer.like');
});

Route::prefix('answer/{answer}')->group(function() {
    Route::resource('comment', 'API\CommentController');
    Route::post('comment/{comment}/like', 'API\CommentController@like')->name('comment.like');
});