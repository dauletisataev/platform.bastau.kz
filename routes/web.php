<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */



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

Route::get('/', function () {
    return view('app');
});

Route::get('/dashboard', function () {
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
    Route::put('/{trainer}','BusinessTrainerController@save');
    // Get one trainer
    Route::get('/{trainer}', 'BusinessTrainerController@item');
    Route::post('/archive/{trainer}', 'BusinessTrainerController@archive');
    Route::delete('/{trainer}', 'BusinessTrainerController@hardDelete');
});

Route::get('/api/users', function() {
    return App\User::all();
});
