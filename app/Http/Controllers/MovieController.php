<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Movie;

class MovieController extends Controller
{
	// public function all()
	// {
    	
 //    	$movies = Movie::all();

 //    	//pretty
 //    	// foreach ($movies as $movie)
 //    	// {
 //    	// 	echo $movie . '<br>';
 //    	// }

 //    	return $movies;
 //    }


    //if name is supplied, get the movie information for the specified movie if found one.
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
    	return $movies;
    }

    //create new movie with information of 'name' and 'duration'
    public function create(Request $request)
    {
    	$name = $request->input('name');
    	if($name != '')
    	{
    		$movie = Movie::where('name', $name)->first();
    		if($movie)
	    	{
	    		return response(view('error', ['text' => "There's already a movie with this name!!"]), 404);
	    	}
    	}
    	else
    	{
    		return response(view('error', ['text' => "Please give movie name"]), 404);
    	}
   	
	   	$duration = $request->input('duration');
    	$newMovie = new Movie;
    	$newMovie->name = $name;
    	$newMovie->duration = $duration;
    	$newMovie->save();

    	return response($newMovie, 201);
      	// echo 'New movie has been added successfully <br />';
		// echo $name . '<br />';
		// echo $duration;
    }

    //update information to the specified movie document.
    //movie name is required, other field can left blank if not change.
    public function update(Request $request)
    {
    	$name = $request->input('name');
    	$newName = $request->input('newName');
    	$newDuration = $request->input('newDuration');
    	$movie = Movie::where('name', $name)->first();
    	if($movie)
    	{
    		if ($newName != '' && $newDuration != '')
    		{
    			if ($newName != '')
    			{
	    			$movie->name = $newName;
	    			//echo '<br>new name set';
    			}
	    		if ($newDuration != '')
	    		{
	    			$movie->duration = $newDuration;
	    			//echo '<br>new duration set';
	    		}
    			$movie->save();
			}
    		return response($movie);
       	}
    	else
    	{
			return response(view('error', ['text' => "Can't find the movie"]), 404);
    	}
    }

	public function deleteMovieByName(Request $request)
	{
    	$name = $request->input('name');
    	$movie = Movie::where('name', $name)->first();
    	if($movie)
    	{
    		$movie->delete();
    		//return $movie;
       	}
    	else
    	{
			return response(view('error', ['text' => 'Movie NOT found?']), 404);
    	}
    }

}
