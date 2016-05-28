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

Route::get('test', function(){
	$str = "a15";
	$match = preg_match('/^[a-zA-Z](1)*[0-9]$/', $str);
	return $match;
	$arr = str_split($str);
	foreach($arr as $x){
		echo $x . ' ';
	}
});

//Page to try some API for schedule planner
Route::get('planner', function(){
	return view('planning');
});

//Page to try some API for Ticket book/buy
Route::get('cashier', function(){
	return view('ticketing');
});

//Movie
//Route::get('movie', "MovieController@all");
Route::post('movie',"MovieController@create");
Route::get('movie', "MovieController@get");
Route::put('movie', "MovieController@update");
Route::delete('movie', "MovieController@deleteMovieByName");

//Schedule
Route::get('schedule',"ScheduleController@all");
Route::get('schedule/{name}', "ScheduleController@getScheduleByMovieName");
Route::get('schedule/{name}/{time}/{theaterNum}', "ScheduleController@getAvailableSeats");
Route::post('schedule', "ScheduleController@newSchedule");
Route::delete('schedule', "ScheduleController@delete");
Route::put('schedule', "ScheduleController@update");

//////////////////Theater
Route::get('theater', "TheaterController@all");
Route::get('theater/{theaterNum}', "TheaterController@get");

//////////////////Ticket managing (Book or buying a ticket)

//
Route::post('reservation', "ScheduleController@reservation"); //I use POST on this as it normally post a new transaction.
Route::delete('reservation', "ScheduleController@purgeReservedSeats");



