<?php

/*
 * TODO Add dynamic lang wildcard
 */
Route::get('/login', function () {
    return view('app');
});

Route::get('/select-account', function () {
    return view('app');
});

Route::get('/reset-pass', function () {
    return view('app');
});

Route::get('/test', function () {
    return view('app');
});
Route::get('/participants', function () {
    return view('app');
});
Route::get('/participant/{path}', function () {
    return view('app');
});
Route::get('/groups', function () {
    return view('app');
});
Route::get('/group/{path}', function () {
    return view('app');
});
Route::get('/', function () {
    return view('app');
});
Route::get('/group/{path1}/add-existing-participant-to-group', function () {
    return view('app');
});

Route::get('/dashboard', function () {
    return view('app');
});

Route::get('/participants', function () {
    return view('app');
});

Route::get('/users', function () {
    return view('app');
});

Route::get('/api/users', function() {
    return App\User::all();
});

Route::get('/user/{id}', function () {
    return view('app');
});

Route::get('/control', function () {
    return view('app');
});

Route::get('/control/{path}', function () {
    return view('app');
});
Route::get('/control/{path}/{path2}', function () {
    return view('app');
});

Route::get('/trainers', function() {
    return view('app');
});

Route::get('/trainers/trainer/{id}', function() {
    return view('app');
});

Route::prefix('/api/trainers')->group(function () {
    // Get all trainers
    Route::get('/', 'BusinessTrainerController@items');
    // Crate or update trainer
    Route::post('/', 'BusinessTrainerController@save');
    Route::put('/update/{trainer}','BusinessTrainerController@save');
    // Get one trainer
    Route::get('/{trainer}', 'BusinessTrainerController@item');
    Route::post('/archive/{trainer}', 'BusinessTrainerController@archive');
    Route::delete('/delete/{trainer}', 'BusinessTrainerController@delete');
});

Route::get('/api/users', function() {
    return App\User::all();
});
