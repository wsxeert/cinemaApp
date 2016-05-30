<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Movie;
use DB;

class MovieController extends Controller
{
    //if 'name' is supplied, get the movie information for the specified movie if found one.
    //otherwise return every movies we have.
    //return type will always be an array even we didnt find any
    public function get()
    {
    	$name = '';

    	if(isset($_GET['name']))
    	{
    		$name = $_GET['name'];
    	}
    	
    	if($name == '')
    	{
    		$movies = Movie::all();
    	}
    	else
    	{
    		$movies = Movie::where('name', $name)->get();
       	}

       	if(count($movies) != 0)
       	{
	     	foreach($movies as $movie)
			{
				$returnList[] = ['name' => $movie->name, 'duration' => $movie->duration];
			}	
       	}
       	else
       	{
       		return response()->json(['status' => 404, 'message' => 'Movie not found'], 404);
       	}
    	
    	return response()->json($returnList);
    }

    //create new movie with information of 'name' and 'duration'
    public function createMovie(Request $request)
    {
    	$name = $request->input('name');
    	$duration = $request->input('duration');
    	if($name != '' && $duration != '')
    	{
    		$movie = Movie::where('name', $name)->first();
    		if($movie)
	    	{
	    		return response()->json(['status' => 404, 'message' => 'There is already a movie with this name'], 404);
	    	}
    	}
    	else
    	{
    		return response()->json(['status' => 404, 'message' => 'Not enough input information'], 404);
    	}
   	
	   	$newMovie = new Movie;
    	$newMovie->name = $name;
    	$newMovie->duration = $duration;
    	$newMovie->save();

    	return response()->json(['name'=> $name, 'duration'=> $duration], 201);
      	// echo 'New movie has been added successfully <br />';
		// echo $name . '<br />';
		// echo $duration;
    }

    //update information to the specified movie document.
    //movie name is required, other field can left blank if not change.
    public function updateMovie(Request $request)
    {
    	$name = $request->input('name');
    	$newName = $request->input('newName');
    	$newDuration = $request->input('newDuration');
    	if(Movie::where('name', $newName)->first())
    	{
    		return response()->json(['status' => 404, 'message' => 'There is already a movie with this name'], 404);
    	}

    	$movie = Movie::where('name', $name)->first();
    	if($movie)
    	{
    		if ($newName != '' || $newDuration != '')
    		{
    			if ($newName != '')
    			{
	    			$movie->name = $newName;
	    			//update all the name for any schdule with this movie.
	    			DB::collection('schedules')->where('name', $name)->update(['name' => $newName]);
    			}
	    		if ($newDuration != '')
	    		{
	    			$movie->duration = $newDuration;
	    		}

    			$movie->save();
			}
    		return response()->json(['name' => $movie->name, 'duration' => $movie->duration]);
       	}
    	else
    	{
			return response()->json(['status' => 404, 'message' => 'Movie not found'], 404);
    	}
    }

	public function deleteMovieByName(Request $request)
	{
    	$name = $request->input('name');
    	$movie = Movie::where('name', $name)->first();
    	if($movie)
    	{
    		$movie->delete();
    		return 0;
       	}
    	else
    	{
			return response()->json(['status' => 404, 'message' => 'Movie not found'], 404);
    	}
    }

}
