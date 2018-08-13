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


Route::resource('qna', 'API\QNAController');

Route::post('auth/login', 'AuthController@login');
Route::post('auth/register', 'AuthController@register');
Route::post('auth/logout', 'AuthController@logout');
Route::get('auth/me', 'AuthController@me');
//Route::post('auth/refresh', [
//    'middleware' => 'jwt.refresh',
//    function($token) {
//        return $token;
//    }
//]);
