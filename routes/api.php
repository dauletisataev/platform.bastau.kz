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
            Route::get( '/participants', 'ParticipantController@items');
            Route::get('/participants/full-delete/{id}',"ParticipantController@fullDelete");
            Route::get( '/participant-archive/reasons','ParticipantController@getArchiveReasonsList');
            Route::post('/participant-save', 'ParticipantController@save');
            Route::post('/participant-field-save/{id}', 'ParticipantController@saveField');
            Route::post('/participant-document-save/{id}','ParticipantController@saveDocument');
            Route::post('participant-document-remove/{id}','ParticipantController@removeDocument');
            Route::get( '/participant/{id}','ParticipantController@item');
            Route::get('//participant/histories/{id}','ParticipantController@getHistories');
            Route::post('/participant-archive/{id}','ParticipantController@delete');
            Route::get( '/participant-activate/{id}','ParticipantController@activate');

            Route::get( '/users', 'UserController@items');
            Route::get( '/user/{id}', 'UserController@item');
            Route::post('/user-save', 'UserController@save');
            Route::post('/user-photo-save', 'UserController@savePhoto');
            Route::get('/users-all', 'UserController@all');

    });

    Route::group(['middleware' => ['isViewer']], function () {
        Route::get('/dashboard', 'MainController@dashboard');
    });
    
    Route::get('/main/data', 'MainController@data');


});
