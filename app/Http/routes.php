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


//Planning stuff
Route::get('planner', function(){
	return view('planning');
});

//Schedule
Route::get('allschedule',"ScheduleController@all");
Route::post('schedule', "ScheduleController@newSchedule");

//Movie
Route::post('movie',"MovieController@create");
Route::get('allmovie', "MovieController@all");
Route::put('movie', "MovieController@update");
Route::delete('movie', "MovieController@deleteMovieByName");

