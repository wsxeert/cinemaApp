<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\Movie;
use App\Theater;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;

class ScheduleController extends Controller
{

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

    //return the newly created schedule if successful.
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
		return $newSchedule;
	}    

	//return 0 on succeed.
	public function delete(Request $request)
	{
		$name = $request->input('name');
		$time = $request->input('time');
		$theaterNum = $request->input('theater');
		if (($name == '') || ($time == '') || ($theaterNum || ''))
		{
			return response(view('error', ['text' => "Please input all information!!"]),404);
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
				return response(view('error', ['text' => "Schedule NOT found"]),404);
			}
			
			return 0;
		}
	}

	//Return the new schedule if the update has been done successfully
	//otherwise, return null.
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
				//try to get the theater model and then update the theater document to update the seats
				$theater = Theater::where('num',intval($newTheaterNum))->first();
				if($theater)
				{
					$schedule->theater = ['num'=> intval($newTheaterNum), 'availableSeats'=> $theater->seats];
				}
				else
				{
					return response(view('error', ['text' => "Theater NOT found"]),404);
				}
				
			}
			$schedule->save();
			return $schedule;
		}
		else
		{
			//return view('error', ['text' => "Schedule NOT found"]);
			return response(view('error', ['text' => "Schedule NOT found"]), 404);
		}
		
	}

	//Return array of available seats if the schedule found
	public function getAvailableSeats($name, $time, $theaterNum)
	{
		$schedule = Schedule::where('name',$name)->where('time',$time)->where('theater.num',intval($theaterNum))->first();
		if($schedule)
		{
			return $schedule->theater['availableSeats'];	
		}
		else
		{
			//return view('error', ['text' => "Schedule NOT found"]);
			return response(view('error', ['text' => "Schedule NOT found"]), 404);
		}
	}

	//Return Seat (SeatRow and SeatNum), if ticket has been successfully bought
	public function reservation(Request $request)
	{
		$name = $request->input('name');
		$time = $request->input('time');
		$seatRow = strtolower($request->input('seatRow'));
		$seatNum = intval($request->input('seatNum'));
		$theaterNum = intval($request->input('theaterNum'));
		$action = strtolower($request->input('action'));
		if(($action != 'book') && ($action != 'buy'))
		{
			return response(view('error', ['text' => "Please specify 'reservation'"]), 404);
		}

		$schedule = Schedule::where('name',$name)->where('time',$time)->where('theater.num', $theaterNum)->first();
		if($schedule)
		{
			//check if this particular theater has that row of seat.
			if(isset($schedule->theater['availableSeats'][$seatRow]))
			{
				$theater = $schedule->theater;
				$index = array_search($seatNum, $theater['availableSeats'][$seatRow]);
				if($index !== false)
				{	
					//generate reservation list, and booking information.
					if($action == 'book')
					{
						//add the seat to the reserved list.
						$theater["reservedSeats"][$seatRow][] = $seatNum;

						//booking id generate and store some info.
						$bookingDoc = new Schedule;
						$bookingDoc->scheduleid = $schedule->_id;
						$bookingDoc->seats = array($seatRow => $seatNum);
						$bookingDoc->bookid = bin2hex(random_bytes(6));
						$bookingDoc->save();
					}
					unset($theater['availableSeats'][$seatRow][$index]);
					$schedule->theater = $theater;
					$schedule->save();
				}
				else
				{
					//return view('error', ['text' => 'Seat <b>'.$seatRow.$seatNum.'</b> is not available']);
					return response(view('error', ['text' => 'Seat <b>'.$seatRow.$seatNum.'</b> is not available']), 404);
				}
			}
			else
			{	
				//return view('error', ['text' => 'Seat Row<b>'.$seatRow.'</b> is not available']);
				return response(view('error', ['text' => 'Seat Row <b>'.$seatRow.'</b> is not available']), 404);
			}
		}
		else
		{
			//return view('error', ['text' => "Schedule NOT found"]);
			return response(view('error', ['text' => "Schedule NOT found"]), 404);
		}

		return response($seatRow.$seatNum, 201);
	}

	public function purgeReservedSeats(Request $request)
	{
		$name = $request->input('name');
		$time = $request->input('time');
		$theaterNum = $request->input('theaterNum');
		$schedule = Schedule::where('name',$name)->where('time',$time)->where('theater.num',intval($theaterNum))->first();
		if($schedule)
		{
			$theater = $schedule->theater;
			if(isset($theater["reservedSeats"]))
			{
				$reservedSeats = $theater["reservedSeats"];
				$reservedRows = array_keys($reservedSeats);
				$theater["availableSeats"] = array_merge_recursive($theater["availableSeats"], $reservedSeats);
				
				unset($theater["reservedSeats"]);

				//remove all booking document for this schedule/theater
				DB::collection('schedules')->where('scheduleid', $schedule->_id)->delete();

				$schedule->theater = $theater;
				$schedule->save();
				return $theater;
			}
			else
			{
				return response(view('error', ['text' => "No reserved seats for this schedule"]), 404);
			}
		}
		else
		{
			response(view('error', ['text' => "Schedule NOT found"]), 404);
		}
	}
}

