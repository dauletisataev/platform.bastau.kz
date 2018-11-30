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



Route::group(['prefix' => 'public'], function() {
    Route::get('/users','UserController@accounts');
    Route::post('/reset-pass/{id}','UserController@resetPass');
    Route::post('/check-reset-code','UserController@checkResetCode');
    Route::post('/send-reset-code/{id}','UserController@sendResetCode');
});

Route::group(['middleware' => ['auth:api']], function () {

    Route::get('/user', 'UserController@authenticated');

    Route::group(['middleware' => ['isEditor']], function () {


        Route::get('/users', 'UserController@items');
        Route::get('/user/{id}', 'UserController@item');
        Route::post('/user-save', 'UserController@save');
        Route::delete('/user-delete/{id}', 'UserController@delete');
        Route::post('/user-photo-save', 'UserController@savePhoto');

        Route::get('/users-all', 'UserController@all');

    });

    Route::group(['middleware' => ['isViewer']], function () {
        Route::get('/dashboard', 'MainController@dashboard');
    });
    Route::get('/main/data', 'MainController@data');
});

Route::get('/lesson-templates/getConnectedCourses/{id}','LessonTemplateController@getItemConnections');
Route::get('/lesson-templates', 'LessonTemplateController@items');
Route::delete('/lesson-template-delete/{id}', 'LessonTemplateController@delete');
Route::post('/lesson-template-save', 'LessonTemplateController@save');
Route::get('/lesson-template/select-options', 'LessonTemplateController@getSelectOptions');

Route::get('/lesson-template-item/{id}', 'LessonTemplateController@item');
Route::post('/lesson-template-item-content-save/{id}', 'LessonTemplateController@saveContent');
Route::post('lesson-template-item-save/{id}','LessonTemplateController@saveItem');
Route::delete('lesson-template-item-delete/{id}','LessonTemplateController@deleteItem');
Route::post('/lesson-template-item-move','LessonTemplateController@move');
Route::get('/lesson-template-item-content/{id}', 'LessonTemplateController@content');

Route::get('/lesson/{id}','LessonController@item');

Route::post('/upload-file','MaterialController@uploadFile');
Route::post('/lesson-template-save-test', 'LessonTemplateController@saveTestQuestion');
