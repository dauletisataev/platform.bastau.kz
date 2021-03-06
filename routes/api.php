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

            //Yersultan::routes for participants
            Route::group(['middleware' => ['isParticipant']],function(){
                Route::get('/participant-groups','GroupController@items');
                Route::get('/participant-lesson/{id}','LessonController@item');
            });
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
                Routes that used for lessons(including attendance)
            */
            Route::get('/lesson/attendance/{id}','LessonController@getLessonAttendance');
            Route::post('/lesson/attendance/save','LessonController@setAttendance');
            Route::get('/lessons/get-pseudotime/{id}','LessonController@getPseudoTime');
            Route::post('/lessons/import-from-templates/{id}',"GroupController@importLessonsFromTemplate");

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

                /* Yersultan
                 * Routes for holidays
                 */
            Route::get('/holidays','HolidayController@items');
            Route::get('/holiday/{id}','HolidayController@item');
            Route::post('/holiday-save','HolidayController@save');
            Route::post('/holiday/delete/{id}','HolidayController@delete');

            Route::get( '/users', 'UserController@items');
            Route::get( '/user/{id}', 'UserController@item');
            Route::post('/user-save', 'UserController@save');
            Route::post('/user-photo-save', 'UserController@savePhoto');
            Route::get('/users-all', 'UserController@all');

            /**
             * Alibek
             * Routes for trainers
             */

    // Get all trainers
    Route::get('trainers/', 'BusinessTrainerController@items');
    // Crate or update trainer
    Route::post('trainers/', 'BusinessTrainerController@save');
    Route::put('trainers/update/{trainer}','BusinessTrainerController@save');
    // Get one trainer
    Route::get('trainers/{trainer}', 'BusinessTrainerController@item');
    Route::post('trainers/archive/{trainer}', 'BusinessTrainerController@archive');
    Route::post('trainers/restore/{trainer}', 'BusinessTrainerController@restore');
    // Route::delete('trainers/delete/{trainer}', 'BusinessTrainerController@delete');

            /*Daulet
            * Routes for sendpulse communications 
            */
            Route::get('/sendpulse/getTemplates', 'SendPulseController@getTemplates');
            Route::post('/sendpulse/sendEmail', 'SendPulseController@sendEmail');
    Route::get('/user', 'UserController@authenticated');

    /**
     * Daulet
     * Comman routes when user is Administrator or Coordinator or BusinessTrainer
     * TODO:  обсудить общие роуты
     */
    Route::group(['middleware' => ['isEditor']], function () {
        Route::get( '/participants', 'ParticipantController@items'); 
        Route::get( '/participant-archive/reasons','ParticipantController@getArchiveReasonsList');//TODO: ?
        Route::post('/participant-save', 'ParticipantController@save');
        Route::post('/participant-field-save/{id}', 'ParticipantController@saveField'); 
        Route::get( '/participant/{id}','ParticipantController@item');
        Route::get( '/participant/histories/{id}','ParticipantController@getHistories');
        Route::post('/participant-archive/{id}','ParticipantController@archive');
        /* 
            Routes that used for groups
        */
        Route::get( '/group/{id}','GroupController@item');
        Route::get('/groups','GroupController@items');
        Route::get('/groups/{id}/add-participants','GroupController@getNotMyParticipants');
        Route::post('/group-save','GroupController@saveGroup');
        Route::post('/group/{id}/add-participants',"GroupController@addParticipants");
        Route::post('/group/{id}/remove-participant',"GroupController@removeParticipant");
        Route::post('/group-archive/{id}','GroupController@archive');

            /* 
                Routes that used for projects
            */
        Route::get('/projects/get-all','ProjectController@getAll');
 
        /*Daulet
        * Routes for sendpulse communications 
        */
        Route::get('/sendpulse/getTemplates', 'SendPulseController@getTemplates');
        Route::post('/sendpulse/sendEmail', 'SendPulseController@sendEmail'); 
        Route::get('/export/participants', 'ParticipantController@exportAll');
    });
    Route::group(['middleware' => ['isAdminOrCoordinator']], function () { 
        Route::get( '/trainer/{id}','BusinessTrainerController@item');
        Route::get('/trainers','BusinessTrainerController@items'); 
    });
    Route::group(['middleware' => ['isAdmin']], function () {  
        
        Route::get('/participants/full-delete/{id}',"ParticipantController@fullDelete");  
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

Route::get('/lesson-templates/courses/{id}','LessonTemplateController@getItemConnections');
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
Route::post('/lesson-save/','LessonController@save');

Route::post('/upload-file','MaterialController@uploadFile');
Route::post('/lesson-template-save-test', 'LessonTemplateController@saveTestQuestion');
Route::post('lesson-template-add-test/{id}','LessonTemplateController@addTestQuestion');
Route::get('lesson-template-delete-test/{id}','LessonTemplateController@deleteTestQuestion');

Route::get('/user-logs', 'UserLogController@items');});