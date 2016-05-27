<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\Movie;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{

	public function home()
	{
		return view('planning');
	}

    public function all()
    {
    	
    	$schedules = Schedule::all();
    	//Pretty
    	//This should be moved to View
   //   	foreach ($schedules as $schedule) {
			// echo $schedule . '<br>';
   // 		}

    	return $schedules;
    }

    public function getScheduleByMovieName($name)
    {
    	$schedules = Schedule::where('name', $name)->get();
    	return $schedules;
    }

	public function newSchedule(Request $request)
	{
		//*****Need to add a checking if the inputs are valid!*****
		$movieName = $request->input('name');
		$movie = Movie::where('name', $movieName)->first();
		if($movie)
		{
			$time = $request->input('time');
			$theater = $request->input('theater');
			if( ($time != '') && ($theater != '') )
			{
				$newSchedule = new Schedule;
				$newSchedule->name = $movieName;
				$newSchedule->time = $time;
				$newSchedule->theater = $theater;
				$newSchedule->save();
				return $newSchedule;
			}
			else
			{
				return view('error', ['text' => "Please input all information"]);
			}
		}
		else
		{
			return view('error', ['text' => "Movie NOT found!"]);
		}
		// echo 'New schedule has been added successfully <br />';
  // 		echo 'Movie name: ' . $newSchedule->name . '<br />';
  //   	echo 'Time: ' . $newSchedule->time . '<br />';
  //    	echo 'Theater: ' . $newSchedule->theater . '<br />';
	}    


	public function delete(Request $request)
	{
		$name = $request->input('name');
		$time = $request->input('time');
		$theater = $request->input('theater');
		if ($name == '')
		{
			return view('error', ['text' => "Please input movie name!!"]);
		}
		else
		{
			$schedule = Schedule::where('name', $name)->where('time',$time)->where('theater',$theater)->first();
			if($schedule)
			{
				$schedule->delete();	
			}
			
			return $schedule;
		}
	}

	public function update(Request $request)
	{
		$schedule = Schedule::where('name',$request->input('name'))->where('time',$request->input('time'))->where('theater',$request->input('theater'))->first();
		if($schedule)
		{
			$newName = $request->input('newName');
			$newTime = $request->input('newTime');
			$newTheater = $request->input('newTheater');
			if($newName != '')
			{	
				$schedule->name = $newName;
			}
			if($newTime != '')
			{	
				$schedule->time = $newTime;
			}
			if($newTheater != '')
			{	
				$schedule->theater = $newTheater;
			}
			$schedule->save();
		}
		else
		{
			return view('error', ['text' => "Schedule NOT found"]);
		}
		
	}
}

