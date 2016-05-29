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
	public function deleteSchedule(Request $request)
	{
		$id = $request->input('_id');
		if($id != '')
		{
			$schedule = Schedule::find($id);
		}
		else
		{
			$name = $request->input('name');
			$time = $request->input('time');
			$theaterNum = $request->input('theater');
			$schedule = Schedule::where('name', $name)->where('time',$time)->where('theater.num',intval($theaterNum))->first();
		}

		if($schedule)
		{
			$schedule->delete();
		}
		else
		{
			return response(view('error', ['text' => "Schedule NOT found"]),404);
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

	public function buySeatsByBookingId($bookingId)
	{
		$bookingDoc = Schedule::where('bookid',$bookingId)->first();

		if($bookingDoc)
		{
			$seatsArray = $bookingDoc->seats;
			if (count($seatsArray) == 0)
			{
				//empty booking????
				return response(view('error', ['text' => "something's wrong with booking system 1"]), 500);
			}

			$schedule = Schedule::find($bookingDoc->scheduleid);
			if (!$schedule)
			{
				//Booking ID is there.... but schedule has gone?
				return response(view('error', ['text' => "something's wrong with booking system 2"]), 500);
			}

			$theater = $schedule->theater;
			$seatRows = array_keys($seatsArray);
			$successList = array();

			//for debugging
			//print_r($theater);
			//print_r($seatsArray);

			foreach ($seatRows as $seatRow)
			{
				//check if this reserve list has that row of seat recorded.
				if(isset($theater['reservedSeats'][$seatRow]))
				{
					$seatNums = $seatsArray[$seatRow];
					foreach($seatNums as $seatNum)
					{
						$index = array_search($seatNum, $theater['reservedSeats'][$seatRow]);
						if($index !== false)
						{
							//for debugging
							//print_r('index:'. $index . ' seat:' . $seatRow . $seatNum . '<br>');
							unset($theater['reservedSeats'][$seatRow][$index]);
							$successList[$seatRow][] = $seatNum;
						}
					}

					if(count($theater['reservedSeats'][$seatRow]) == 0)
					{
						//clear it just for cleaness
						unset($theater['reservedSeats'][$seatRow]);
					}
				}
			}
			if(count($theater['reservedSeats']) == 0)
			{
				//clear it just for cleaness
				unset($theater['reservedSeats']);
			}
		}
		else
		{
			return response(view('error', ['text' => "Booking ID NOT found"]), 404);
		}
		
		//for debugging
		//print_r($theater);
		if(count($successList) != 0)
		{

			$schedule->theater = $theater;
			$schedule->save();
			$bookingDoc->delete();
		}

		return $successList;
	}

	
	//make the input string, separate them to be array of ["row"=>"seat"]
	public function makeArrayOfSeatsFromText($seats)
	{
		$seatsInText = strtolower($seats);
		$seatsTextInArray = explode(',', $seatsInText);
		$seatsArray = array();
		foreach ($seatsTextInArray as $seatText)
		{
			//only use the text that has valid seat pattern. i.e. A1 A2 A99 Z1 Z99
			if(preg_match("/^[a-zA-Z]+\d\d?$/", $seatText) != 0)
			{
				$seatsArray[$seatText[0]][] = intval(substr($seatText,1,2));
			}
		}
		return $seatsArray;
	}


	public function reserveSeats(Request $request, $action='buying')
	{
		$name = $request->input('name');
		$time = $request->input('time');
		$theaterNum = intval($request->input('theaterNum'));
		
		//0. find if schedule is there, if not just return
		$schedule = Schedule::where('name',$name)->where('time',$time)->where('theater.num', $theaterNum)->first();
		if(!$schedule)
			return response(view('error', ['text' => "Schedule NOT found"]), 404);

		//1. get Seats input, make it to array of seats.
		$seatsArray = ScheduleController::makeArrayOfSeatsFromText($request->input('seats'));

		if(count($seatsArray) != 0)
		{
			$successList = ScheduleController::occupySeats($schedule, $seatsArray, $action);
		}
		else
		{
			return response(view('error', ['text' => "Invalid Seat to buy"]), 404);
		}

		return $successList;

	}

	//Try multiple seats per booking transaction
	public function occupySeats($schedule, $seatsArray, $action)
	{	
		$seatrows = array_keys($seatsArray);
		//print_r($seatsArray);

		$theater = $schedule->theater;
		$successList = array();
		
		if($action == 'booking')
		{
			$bookingDoc = new Schedule;
			$bookingDoc->scheduleid = $schedule->_id;
		}

		//loop through all seats
		foreach ($seatrows as $seatRow)
		{
			//Theaters are different, some big, some small, check if there's the specified row.
			if(isset($theater['availableSeats'][$seatRow]))
			{
				$seatRowNums = $seatsArray[$seatRow];
				foreach ($seatRowNums as $seatNum)
				{
					//find the seatnum if it's available. (if it has been bought it should have already been removed some time ago)
					$index = array_search($seatNum, $theater['availableSeats'][$seatRow]);
					if($index !== false)
					{
						//remove each seat from the available list
						unset($theater['availableSeats'][$seatRow][$index]);
						$theater["reservedSeats"][$seatRow][] = $seatNum;
						$successList[$seatRow][] = $seatNum;
					}
				}
			}
		}
		if (count($successList) != 0)
		{
			if($action == 'booking')
			{
				$bookingDoc->seats = $successList;
				//carry out the bookingID
				$action = bin2hex(random_bytes(6));
				$bookingDoc->bookid = $action;
				$bookingDoc->save();
			}
		
			//push to database
			$schedule->theater = $theater;
			$schedule->save();

			//for debugging
			// print_r($successList);
			// print_r($bookingDoc);
			// print_r($theater);
			return [$successList, $action];
		}
		else
		{
			return response(view('error', ['text' => "Seats are not available"]), 404);
		}
	}

	public function reservation(Request $request)
	{
		$action = strtolower($request->input('action'));
		
		if($action == 'buy')
		{
			$bookingId = $request->input('bookingid');
			if($bookingId != '')
			{
				return ScheduleController::buySeatsByBookingId($bookingId);
			}
			else
			{
				return ScheduleController::reserveSeats($request);
			}
		}
		elseif ($action == 'book')
		{
			return ScheduleController::reserveSeats($request, 'booking');
		}
	}


	public function purgeReservedSeats(Request $request)
	{
		//if ID is supplied, then it will ignore the rest info
		//this is intended just for the one who know deeply about his own database.
		$id = $request->input('_id');
		if($id != '')
		{
			$schedule = Schedule::find($id);
		}
		else{
			$name = $request->input('name');
			$time = $request->input('time');
			$theaterNum = $request->input('theaterNum');
			$schedule = Schedule::where('name',$name)->where('time',$time)->where('theater.num',intval($theaterNum))->first();
		}

		if($schedule)
		{
			$theater = $schedule->theater;

			//remove all booking document for this schedule/theater
			//put it here just in case that the reservedSeats mismatch with booking document. (no reserved seats in theater's document for some reason)
			//just delete them all
			DB::collection('schedules')->where('scheduleid', $schedule->_id)->delete();

			if(isset($theater["reservedSeats"]))
			{
				$reservedSeats = $theater["reservedSeats"];
				$reservedRows = array_keys($reservedSeats);
				$theater["availableSeats"] = array_merge_recursive($theater["availableSeats"], $reservedSeats);
				
				unset($theater["reservedSeats"]);

				$schedule->theater = $theater;
				$schedule->save();
			}
			else
			{
				return response(view('error', ['text' => "No reserved seats for this schedule"]), 404);
			}
		}
		else
		{
			return response(view('error', ['text' => "Schedule NOT found"]), 404);
		}

		return $theater;
		//return $theater;
	}

	public function findSeatFromBookingId($bookingId)
	{
		$reservedInfo = Schedule::where('bookid',$bookingId)->first();
		if($reservedInfo)
		{
			$seatRow = array_keys($reservedInfo->seats)[0];
			$seatNum = $reservedInfo->seats[$seatRow];
			$schedule = Schedule::find($reservedInfo->scheduleid);
			$movieName = $schedule->name;
			return response()->json(['bookingID'=>$bookingId, 'name'=>$movieName, 'theater'=>$schedule->theater['num'],'seat'=>$seatRow.$seatNum]);
		}
		else
		{
			return response(view('error', ['text' => "Invalid Booking ID"]), 404);
		}
	}
}

