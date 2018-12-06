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

            /*Yersultan
                    Routes that used for participants
              */
            Route::get('/participants/full-delete/{id}',"ParticipantController@fullDelete");
            Route::get( '/participant-archive/reasons','ParticipantController@getArchiveReasonsList');
            Route::post('/participant-save', 'ParticipantController@save');
            Route::post('/participant-field-save/{id}', 'ParticipantController@saveField');
            Route::post('/participant-document-save/{id}','ParticipantController@saveDocument');
            Route::post('.participant-document-remove/{id}','ParticipantController@removeDocument');
            Route::get( '/participant/{id}','ParticipantController@item');
            Route::get( '/participant/histories/{id}','ParticipantController@getHistories');
            Route::post('/participant-archive/{id}','ParticipantController@archive');
            /* Yersultan
                Routes that used for groups
            */
            Route::get( '/group/{id}','GroupController@item');
            Route::get('/groups','GroupController@items');
            Route::get('/groups/{id}/add-participants','GroupController@getNotMyParticipants');
            Route::post('/group-save','GroupController@saveGroup');
            Route::post('/group/{id}/add-participants',"GroupController@addParticipants");
            Route::post('/group/{id}/remove-participant',"GroupController@removeParticipant");
            Route::post('/group-archive/{id}','GroupController@archive');

                /* Yersultan
                    Routes that used for projects
                */
            Route::get('/projects/get-all','ProjectController@getAll');

                /* Yersultan
                 * Routes for regions/districts or localities
                 */
            Route::post('/region/save','RegionController@save');
            Route::post('/region/delete/{id}','RegionController@delete');
            Route::post('/region/update','RegionController@update');
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

Route::get('/lesson-templates/getConnectedCourses/{id}','LessonTemplateController@getItemConnections');
Route::get('/lesson-templates', 'LessonTemplateController@items');
Route::get('/lesson-template-delete/{id}', 'LessonTemplateController@delete');
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
Route::post('lesson-template-add-test/{id}','LessonTemplateController@addTestQuestion');
Route::get('lesson-template-delete-test/{id}','LessonTemplateController@deleteTestQuestion');
