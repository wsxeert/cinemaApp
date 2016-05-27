<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\Movie;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{

	public function home(){
		return view('planning');
	}

    public function all(){
    	
    	$schedules = Schedule::all();
    	//Pretty
    	//This should be moved to View
   //   	foreach ($schedules as $schedule) {
			// echo $schedule . '<br>';
   // 		}

    	return $schedules;
    }

	public function newSchedule(Request $request){
		
		//*****Need to add a checking if the inputs are valid!*****

		$newSchedule = new Schedule;
		$newSchedule->name = $request->input('name');
		$newSchedule->time = $request->input('time');
		$newSchedule->theater = $request->input('theater');
		$newSchedule->save();

		echo 'New schedule has been added successfully <br />';
  		echo 'Movie name: ' . $newSchedule->name . '<br />';
    	echo 'Time: ' . $newSchedule->time . '<br />';
     	echo 'Theater: ' . $newSchedule->theater . '<br />';
	}    

}

