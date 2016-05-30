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
	//HACK function
	//public function showMeAll()
	//{
	//	$schedule = Schedule::all();
	//	return $schedule;
	//}

    public function all()
    {
    	
    	$schedules = Schedule::where('bookid', 'exists', false)->get();
    	
    	foreach($schedules as $schedule)
    	{
    		$theater = $schedule->theater;
    		$returnList[] = ['name' => $schedule->name, 'date' => $schedule->date, 'time' => $schedule->time, 'theater' => $theater['num']];
    	}

    	return $returnList;
    }


    public function getScheduleByMovieNameAndDate($name, $date='')
    {
    	if($date == '')
    	{
    		$schedules = Schedule::where('name', $name)->get();
    	}
    	else
    	{
    		$schedules = Schedule::where('name', $name)->where('date',$date)->get();
    	}

    	if(count($schedules) != 0)
    	{
    		foreach($schedules as $schedule)
	    	{
	    		$returnList[] = ['name'=> $schedule->name, 'date' => $schedule->date, 'time' => $schedule->time, 'theater' => $schedule->theater['num']];
	    	}	
    	}
    	else
    	{
    		return response()->json(['status' => 404,'message' => 'Schedule not found'], 404);
    	}
    	
    	return $returnList;
    }

    //return the newly created schedule if successful.
	public function newSchedule(Request $request)
	{
		$movieName = $request->input('name');
		$time = $request->input('time');
		$date = $request->input('date');
		$theaterNum = $request->input('theater');
		$scheduleFound = Schedule::where('name',$movieName)->where('date',$date)->where('time',$time)->where('theater.num',intval($theaterNum))->first();
		
		//if this new schedule is a duplicated one, then dont create more.
		if($scheduleFound)
		{
			return response()->json(['status' => 404, 'message' => 'Duplcate schedule'], 404);
		}
		
		//allow only existing movies in the database to be added
		$movie = Movie::where('name', $movieName)->first();
		if($movie)
		{
			
			$theater = Theater::where('num',intval($theaterNum))->first();
			if(!$theater)
				return response()->json(['status' => 404,'message' => 'Invalid theater'], 404);

			if( ($time  != '') && ($date != ''))
			{
				$newSchedule = new Schedule;
				$newSchedule->name = $movieName;
				$newSchedule->time = $time;
				$newSchedule->date = $date;
				$newSchedule->theater = ['num'=>intval($theaterNum), 'availableSeats' => $theater->seats];
				$newSchedule->save();
			}
			else
			{
				return response()->json(['status' => 404,'message' => 'Please input all information'], 404);
			}
		}
		else
		{
			return response()->json(['status' => 404, 'message' => 'Movie not found!'], 404);
		}
		return response()->json(['name'=>$newSchedule->name, 'date' => $newSchedule->date, 'time' => $newSchedule->time, 'theater' => $newSchedule->theater['num']]);
	}    

	
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
			$date = $request->input('date');
			$time = $request->input('time');
			$theaterNum = $request->input('theater');
			$schedule = Schedule::where('name', $name)->where('date', $date)->where('time',$time)->where('theater.num',intval($theaterNum))->first();
		}

		if($schedule)
		{
			$schedule->delete();
		}
		else
		{
			return response()->json(['status' => 404, 'message' => 'Schedule not found'], 404);
		}
		return 'SUCCESS';
	}

	//Return the new schedule if the update has been done successfully
	//otherwise, return null.
	public function updateSchedule(Request $request)
	{
		$schedule = Schedule::where('name',$request->input('name'))->where('date', $request->input('date'))->where('time',$request->input('time'))->where('theater.num',intval($request->input('theater')))->first();
		if($schedule)
		{
			$newName = $request->input('newName');
			$newDate = $request->input('newDate');
 			$newTime = $request->input('newTime');
			$newTheaterNum = $request->input('newTheater');
			
			if(($newName == '') && ($newDate == '') && ($newTime == '') && ($newTheaterNum == ''))
			{
				return response()->json(['status' => 404, 'message' => 'Updating fields were not filled'], 404);
			}

			if($newName != '')
			{	
				$schedule->name = $newName;
			}
			if($newDate != '')
			{	
				$schedule->date = $newDate;
			}
			if($newTime != '')
			{	
				$schedule->time = $newTime;
			}
			if($newTheaterNum != '')
			{	
				$newNum = intval($newTheaterNum);
				//try to get the theater model and then update the theater document to update the seats
				$theater = Theater::where('num',$newNum)->first();
				if($theater)
				{

					$schedule->theater = ['num'=>$newNum, 'availableSeats' => $theater->seats];
				}
				else
				{
					return response()->json(['status' => 404, 'message' => 'Theater not found'], 404);
				}
				
			}
			$foundSchedule = Schedule::where('name',$schedule->name)->where('date',$schedule->date)->where('time',$schedule->time)->where('theater.num', $schedule->theater['num'])->first();
			if($foundSchedule)
			{
				return response()->json(['status' => 404, 'message' => 'Duplicate Schedule'], 404);
			}

			//no problem, then save;
			$schedule->save();
			return response()->json(['name' => $schedule->name, 'date' => $schedule->date, 'time' => $schedule->time, 'theater' => $schedule->theater['num']]);
		}
		else
		{
			//return view('error', ['text' => "Schedule not found"]);
			return response()->json(['status' => 404, 'message' => 'Schedule not found'], 404);
		}
		
	}

	public function getTheater($name, $date, $time)
	{
		$schedules = Schedule::where('name',$name)->where('date',$date)->where('time',$time)->get();
		$returnList = array();
		if(count($schedules) != 0)
		{
			foreach ($schedules as $schedule)
			{
				$returnList[] = $schedule->theater['num'];
			}	
		}
		else
		{
			return response()->json(['status' => 404, 'message' => 'Schedule not found'], 404);
		}

		return $returnList;
	}

	//Return array of available seats if the schedule found
	public function getAvailableSeats($name, $date, $time, $theaterNum='')
	{	
		$returnList = array();

		$schedule = Schedule::where('name',$name)->where('date',$date)->where('time',$time)->where('theater.num',intval($theaterNum))->first();
		
		if($schedule)
		{
			$returnList = $schedule->theater['availableSeats'];	
		}
		else
		{
			//return view('error', ['text' => "Schedule not found"]);
			return response()->json(['status' => 404, 'message' => 'Schedule not found'], 404);
		}
		
		return $returnList;
		
	}

	protected function buySeatsByBookingId($bookingId)
	{
		$bookingDoc = Schedule::where('bookid',$bookingId)->first();

		if($bookingDoc)
		{
			$seatsArray = $bookingDoc->seats;
			if (count($seatsArray) == 0)
			{
				//empty booking????
				return response()->json(['status' => 500, 'text' => 'Bad booking ID'], 500);
			}

			$schedule = Schedule::find($bookingDoc->scheduleid);
			if (!$schedule)
			{
				//Booking ID is there.... but schedule has gone?
				return response()->json(['status' => 500, 'text' => 'Could not find schedule'], 500);
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
			return response()->json(['status' => 404, 'message' => 'Booking ID not found'], 404);
		}
		
		//for debugging
		//print_r($theater);
		if(count($successList) != 0)
		{

			$schedule->theater = $theater;
			$schedule->save();
			$bookingDoc->delete();
		}

		return ['name'=> $schedule->name, 'date' => $schedule->date, 'time' => $schedule->time, 'theater' => $theater['num'], 'seats' => $successList];
	}

	
	//make the input string, separate them to be array of ["row"=>"seat"]
	protected function makeArrayOfSeatsFromText($seats)
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


	//Try multiple seats per booking transaction
	protected function occupySeats($schedule, $seatsArray, $action)
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
						if($action =='booking')
						{
							$theater["reservedSeats"][$seatRow][] = $seatNum;	
						}
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
			
		}

		return ['name' => $schedule->name, 'date' => $schedule->date, 'time' => $schedule->time, 'theater' => $theater['num'], 'seats' => $successList, 'bookingid' => $action];
	}

	protected function reserveSeats(Request $request, $action='buying')
	{
		$name = $request->input('name');
		$date = $request->input('date');
		$time = $request->input('time');
		$theaterNum = intval($request->input('theaterNum'));
		
		//0. find if schedule is there, if not just return
		$schedule = Schedule::where('name',$name)->where('date',$date)->where('time',$time)->where('theater.num', $theaterNum)->first();
		if(!$schedule)
			return response()->json(['status' => 404, 'message' => 'Schedule not found'], 404);

		//1. get Seats input, make it to array of seats.
		$seatsArray = ScheduleController::makeArrayOfSeatsFromText($request->input('seats'));

		if(count($seatsArray) != 0)
		{
			$successList = ScheduleController::occupySeats($schedule, $seatsArray, $action);
		}
		else
		{
			return response()->json(['status' => 404, 'message' => 'Invalid Seat Input'], 404);
		}

		if(count($successList['seats']) != 0)
		{
			return response()->json($successList);	
		}
		else
		{
			return response()->json(['status' => 404, 'message' => 'Specified seats are not available'], 404);
		}
	}

	//Main function to do booking buy the seats
	//it accept bookingID which could be used at booking counter
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


	public function cancelBooking(Request $request)
	{
		$bookingId = $request->input('bookid');
		$reservedInfo = Schedule::where('bookid', $bookingId)->first();
		if($reservedInfo)
		{
			$schedule = Schedule::find($reservedInfo->scheduleid);
			if($schedule)
			{
				$theater = $schedule->theater;

				$bookedSeats = $reservedInfo->seats;
				$bookedRows = array_keys($bookedSeats);
				foreach($bookedRows as $row)
				{
					if(isset($theater['reservedSeats'][$row]) && isset($theater['availableSeats'][$row]))
					{
						$seats = $bookedSeats[$row];
						foreach($seats as $seat)
						{
							$index = array_search($seat, $theater['reservedSeats'][$row]);
							if($index !== false)
							{
								unset($theater['reservedSeats'][$row][$index]);
								$theater['availableSeats'][$row][] = $seat;
								//for debugging
								//$successList[$row][] = $seat;
							}
							else
							{
								//again, for some reason the seat disappeared from the reserved list of the theater document.
							}
						}
						if(count($theater['reservedSeats'][$row]) == 0)
						{
							//clear it just for cleaness
							unset($theater['reservedSeats'][$row]);
						}
					}
					else
					{
						// the booked seats have gone for some reason??
						// this theater somehow does not have that row?? (someone just changed it??)
						// we dont care about it for now.
					}

				}

				if(count($theater['reservedSeats']) == 0)
				{
					//clear it just for cleaness
					unset($theater['reservedSeats']);
				}

				$schedule->theater = $theater;
				$schedule->save();
				$reservedInfo->delete();

			}
			else
			{
				return response()->json(['status' => 404, 'message' => 'Schedule not found, probably schedule time has passed'], 404);
			}

			return 'SUCCESS';
		}
		else
		{
			return response()->json(['status' => 404, 'message' => 'Invalid booking ID'], 404);
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
		else
		{
			$name = $request->input('name');
			$date = $request->input('date');
			$time = $request->input('time');
			$theaterNum = $request->input('theaterNum');
			$schedule = Schedule::where('name',$name)->where('date',$date)->where('time',$time)->where('theater.num',intval($theaterNum))->first();
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
				return response()->json(['status' => 404, 'message' => 'No reserved seats to purge'], 404);
			}
		}
		else
		{
			return response()->json(['status' => 404, 'message' => 'Schedule not found'], 404);
		}

		return 'SUCCESS';
	}

	public function findSeatFromBookingId($bookingId)
	{
		$reservedInfo = Schedule::where('bookid',$bookingId)->first();
		if($reservedInfo)
		{
			$schedule = Schedule::find($reservedInfo->scheduleid);
			return response()->json(['bookingID' => $bookingId, 'name'=>$schedule->name, 'date'=>$schedule->date, 'time'=> $schedule->time, 'theater'=>$schedule->theater['num'],'seat' => $reservedInfo->seats]);
		}
		else
		{
			return response()->json(['status' => 404, 'message' => 'Invalid booking ID'], 404);
		}
	}

	public function findAllBookingInfo()
	{
		$bookingDocs = Schedule::where('bookid', 'exists', true)->get();
		$returnList = array();
		foreach($bookingDocs as $bookingDoc)
		{
			$schedule = Schedule::find($bookingDoc->scheduleid);
			$returnList[] = ['bookingid' => $bookingDoc->bookid, 'name' => $schedule->name, 'date' => $schedule->date, 'time' => $schedule->time, 'theater' => $schedule->theater['num'], 'seats' => $bookingDoc->seats];
		}

		return $returnList;
	}
}

