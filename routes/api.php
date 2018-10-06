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

Route::post('social/login/{provider}', 'SocialController@socialLogin')->name('social.login');
Route::post('social/register', 'SocialController@socialRegister')->name('social.register');

Route::get('category', 'API\CategoryController@index')->name('category.index');

Route::resource('qna', 'API\QNAController');
Route::get('auth/question', 'API\QNAController@myQuestion')->name('question.myQuestion');
Route::get('category/search/{category_id}', 'API\QNAController@categorySearch')->name('category.search');

Route::prefix('question/{question}')->group(function() {
    Route::resource('answer', 'API\AnswerController');
    Route::post('answer/{answer}/like', 'API\AnswerController@like')->name('answer.like');
    Route::post('answer/{answer}/select', 'API\AnswerController@select')->name('answer.select');
});

Route::prefix('answer/{answer}')->group(function() {
    Route::resource('comment', 'API\CommentController');
});

Route::post('search', 'API\QNAController@search');

//Route::post('notification', 'API\NotificationController@postToken');
//Route::post('notification/send', 'API\NotificationController@sendNotification');

Route::get('/me', function (Request $request) {
    return (array) $request->user();
})->middleware('auth:api');


Route::post('notification', 'API\NotificationController@sendNotification');
Route::get('notification', 'API\NotificationController@index');
Route::get('notification/{sender_id}/{receiver_id}', 'API\NotificationController@show');

Event::listen('sendToFirebase', function($token, $message) {
    $url = 'https://fcm.googleapis.com/fcm/send';
    $fields = array(
        'to' => $token,
        'data' => $message
    );

    // Firebase Server Key
    $headers = array(
        'Authorization: key=AAAANieYVLo:APA91bEa4c8h0C2S5rzC3OPDooBVE8NMDGKAD451VdcsjcufiIqOjed9XbatLy85L4iThYGo_VeRzn5cAnYOCTQZ3i9DZ2fYEVCIBm3uvmh_qwxBPpnPaZUuOZfw5Zy4fzNlFPLJbDC6',
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);

    if ($result == FALSE) {
        die('CURL failed: ' . curl_error($ch));
    }

    curl_close($ch);

    return $result;
});