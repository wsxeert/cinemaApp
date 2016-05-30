<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Theater;
use DB;

class TheaterController extends Controller
{
	public function getAllTheater()
	{
		$theaters = Theater::all();
		$returnList = array();
		foreach($theaters as $theater)
		{
			$returnList[] = ['num' => $theater['num'], 'seats' => $theater['seats']];
		}

		return $returnList;
	}

    public function getTheater($theaterNum)
    {
    	$theater = Theater::where('num',intval($theaterNum))->first();
    	if($theater)
    	{
    		return ['num' => $theater['num'], 'seats' => $theater['seats']];
    	}
    	else
    	{
    		return response()->json(['status' => 404, 'message' => 'Theater number invalid'], 404);
    	}
	}

	protected function seatsTableGen(int $rowsNum, int $seatsPerRow)
	{
		$startrow = ord('a');
		$seatArray = array();
		foreach(range(0,$rowsNum-1) as $i)
		{
			$seatArray[chr($startrow + $i)] = range(1, $seatsPerRow);
		}

		return $seatArray;
	}

	public function createTheater(Request $request)
	{
		$num = $request->input('num');
		$rows = $request->input('rows');
		$seatsPerRow = $request->input('seats');

		if(($num == '') || ($rows == '') || ($seatsPerRow == ''))
		{
			return response()->json(['status' => 404, 'message' => 'Not enough information'], 404);
		}

		//reuse the same name and make them to integer type
		//check if there is any invalid information
		$num = intval($num);
		$rows = intval($rows);
		$seatsPerRow = intval($seatsPerRow);
		if($num <= 0 || ($rows <= 0) || ($seatsPerRow <= 0) )
			return response()->json(['status' => 404, 'message' => 'Invalid information supplied'], 404);
		if (Theater::where('num', $num)->first())
			return response()->json(['status' => 404, 'message' => 'There is already a theater with this number'], 404);

		//generate array of seats here		
		$seatsArray = TheaterController::seatsTableGen($rows, $seatsPerRow);

		if(count($seatsArray))
		{
			$newTheater = new Theater;
			$newTheater['num'] = $num;
			$newTheater['seats'] = $seatsArray;
			$newTheater->save();
			return response()->json(['num' => $num, 'seats' => $seatsArray]);	
		}
		else
		{
			return response()->json(['status' => 404, 'message' => 'There is already a theater with this number'], 404);
		}
	}

	public function deleteTheater(Request $request)
	{
		$num = intval($request->input('num'));
		$theater = Theater::where('num', $num)->first();
		if($theater)
		{
			$theater->delete();

			//Cross check with the Scheduler documents if there any schedule with this specified theater number left
			$recursive = $request->input('recursive');
			if($recursive == 'yes')
			{
				DB::collection('schedules')->where('theater.num', $num)->delete();
			}
		}
		else
		{
			return response()->json(['status' => 404, 'message' => 'Theater not found'], 404);
		}
		return 0;
	}
}

