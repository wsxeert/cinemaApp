<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\Movie;
use App\Theater;
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
		$movieName = $request->input('name');
		$time = $request->input('time');
		$theaterNum = $request->input('theater');
		$scheduleFound = Schedule::where('name',$movieName)->where('time',$time)->where('theater.num',intval($theaterNum))->first();
		//if this new schedule is a duplicated one, then dont create more.
		if($scheduleFound)
		{
			return view('error', ['text' => "Duplcate schedule"]);
		}
		
		//allow only existing movies in the database to be added
		$movie = Movie::where('name', $movieName)->first();
		if($movie)
		{
			
			$theater = Theater::where('num',intval($theaterNum))->first();
			if( ($time != '') && ($theater) )
			{
				$newSchedule = new Schedule;
				$newSchedule->name = $movieName;
				$newSchedule->time = $time;
				$newSchedule->theater = ['num'=>intval($theaterNum), 'availableSeats' => $theater->seats];
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
		$theaterNum = $request->input('theater');
		if (($name == '') || ($time == '') || ($theaterNum || ''))
		{
			return view('error', ['text' => "Please input all information!!"]);
		}
		else
		{
			//only allow to delete when all fields received.
			$schedule = Schedule::where('name', $name)->where('time',$time)->where('theater.num',intval($theaterNum))->first();
			if($schedule)
			{
				$schedule->delete();
			}
			else
			{
				return view('error', ['text' => "Schedule NOT found"]);
			}
			
			return $schedule;
		}
	}

	public function update(Request $request)
	{
		$schedule = Schedule::where('name',$request->input('name'))->where('time',$request->input('time'))->where('theater.num',intval($request->input('theater')))->first();
		if($schedule)
		{
			$newName = $request->input('newName');
			$newTime = $request->input('newTime');
			$newTheaterNum = $request->input('newTheater');

			if($newName != '')
			{	
				$schedule->name = $newName;
			}
			if($newTime != '')
			{	
				$schedule->time = $newTime;
			}
			if($newTheaterNum != '')
			{	
				$theater = Theater::where('num',intval($newTheaterNum))->first();
				if($theater)
				{
					$schedule->theater = ['num'=> intval($newTheaterNum), 'availableSeats'=> $theater->seats];
				}
				else
				{
					return view('error', ['text' => "Theater NOT found"]);
				}
				
			}
			$schedule->save();
		}
		else
		{
			return view('error', ['text' => "Schedule NOT found"]);
		}
		
	}

	public function getAvailableSeats($name, $time)
	{
		$schedules = Schedule::where('name',$name)->where('time',$time)->get()->sortBy('theater.num');
		$arr = array();
		foreach ($schedules as $schedule)
		{
			$arr[] = $schedule->theater;
		}
		return $arr;
	}

	public function buyTicket(Request $request)
	{
		$name = $request->input('name');
		$time = $request->input('time');
		$seat = $request->input('seat');
		return "Buy a ticket";
	}
}

