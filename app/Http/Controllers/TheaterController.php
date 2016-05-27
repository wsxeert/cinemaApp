<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Theater;

class TheaterController extends Controller
{
	public function all()
	{
		return Theater::all();
	}

    public function get($theaterNum)
    {
    	$theater = Theater::where('num',intval($theaterNum))->first();
    	if($theater)
    	{
    		return $theater;
    	}
	}
}

