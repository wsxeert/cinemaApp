<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


//Page to try some API for schedule planner
Route::get('planner', function(){
	return view('planning');
});

//Movie
Route::post('movie',"MovieController@create");
Route::get('movieall', "MovieController@all");
Route::get('movie/{name}', "MovieController@get");
Route::put('movie', "MovieController@update");
Route::delete('movie', "MovieController@deleteMovieByName");

//Schedule
Route::get('schedule',"ScheduleController@all");
Route::get('schedule/{name}', "ScheduleController@getScheduleByMovieName");
Route::post('schedule', "ScheduleController@newSchedule");
Route::delete('schedule', "ScheduleController@delete");
Route::put('schedule', "ScheduleController@update");



