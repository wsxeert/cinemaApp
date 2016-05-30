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
Route::get('planning', function(){
	return view('planning');
});

//Page to try some API for Ticket book/buy
Route::get('counter', function(){
	return view('ticketing');
});


//Route::get('showmeall', "ScheduleController@showMeAll"); //HACK 
//Route::get('movie', "MovieController@all");

///////////////////Manage Movie Information/////////////////////
Route::post('planning/movie/create',"MovieController@createMovie");
Route::get('planning/movie', "MovieController@get");
Route::put('planning/movie/update', "MovieController@updateMovie");
Route::delete('planning/movie/delete', "MovieController@deleteMovieByName");

//////////////////Manage Theater Information/////////////////////
Route::get('planning/theater', "TheaterController@getAllTheater");
Route::get('planning/theater/{theaterNum}', "TheaterController@getTheater");
Route::post('planning/theater/create', "TheaterController@createTheater");
Route::delete('planning/theater/delete', "TheaterController@deleteTheater");

//////////////////Manage Schedule////////////////////
Route::get('planning/schedule',"ScheduleController@all");									//just an alternate to check schedules for planner.
Route::post('planning/schedule/create', "ScheduleController@newSchedule");					
Route::delete('planning/schedule/delete', "ScheduleController@deleteSchedule");
Route::put('planning/schedule/update', "ScheduleController@updateSchedule");

Route::get('schedule/all',"ScheduleController@all");														//used by mobile and cashier.
Route::get('schedule/movie/{name}', "ScheduleController@getScheduleByMovieNameAndDate");					//used by mobile and cashier.
Route::get('schedule/movie/{name}/{date}', "ScheduleController@getScheduleByMovieNameAndDate");				//used by mobile and cashier.
Route::get('schedule/movie/{name}/{date}/{time}/', "ScheduleController@getTheater");						//used by mobile and cashier.
Route::get('schedule/movie/{name}/{date}/{time}/{theaterNum}', "ScheduleController@getAvailableSeats");		//used by mobile and cashier.

/////////////////////////Reservation (Book/buy tickets)/////////////////////////////////

//these 2 route are only for booking counter/cashier to use.
Route::get('counter/reservation/info', "ScheduleController@findAllbookingInfo");
//this can be used for both bookingID and purchaseID
Route::get('counter/reservation/info/{bookingId}', "ScheduleController@findSeatFromBookingId");
//This function do both Buying and Booking, I use POST on this as it normally post a new transaction.
Route::post('counter/reservation/reserveSeats', "ScheduleController@reservation");
Route::post('counter/reservation/claimTicket', "ScheduleController@claimTicket");

//A route for other client to do reservation, i.e. online booking/mobile app
Route::post('remote/reservation/reserveSeats', "ScheduleController@remoteReservation");
Route::delete('remote/reservation/delete', "ScheduleController@cancelBooking");

//this will most likely be used by an automate system that run at 45 min before schedule
Route::delete('reservation/delete/theater', "ScheduleController@purgeReservedSeats");		
