<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Movie;

class MovieController extends Controller
{
	public function all()
	{
    	
    	$movies = Movie::all();

    	//pretty
    	// foreach ($movies as $movie)
    	// {
    	// 	echo $movie . '<br>';
    	// }

    	return $movies;
    }

    public function get($name)
    {
    	echo $name;
    	$movie = Movie::where('name', $name)->first();
    	return $movie;
    }


    public function create(Request $request)
    {
    	$name = $request->input('name');
    	if($name != '')
    	{
    		$movie = Movie::where('name', $name)->first();
    		if($movie)
	    	{
	    		return view('error', ['text' => "There's already a movie with this name!!"]);
	    	}
    	}
    	else
    	{
    		return view('error', ['text' => "Please give movie name"]);
    	}
   	
	   	$duration = $request->input('duration');
    	$newMovie = new Movie;
    	$newMovie->name = $name;
    	$newMovie->duration = $duration;
    	$newMovie->save();

    	return $newMovie;
      	// echo 'New movie has been added successfully <br />';
		// echo $name . '<br />';
		// echo $duration;
    }

    public function update(Request $request)
    {
    	$name = $request->input('name');
    	$movie = Movie::where('name', $name)->first();
    	if($movie)
    	{
    		$newName = $request->input('newName');
    		if ($newName != ''){
    			$movie->name = $newName;
    			//echo '<br>new name set';
    		}
    		$newDuration = $request->input('newDuration');
    		if ($newDuration != ''){
    			$movie->duration = $newDuration;
    			//echo '<br>new duration set';
    		}
    		$movie->save();
    		//echo 'Saved.';
    		//return $movie;
       	}
    	else
    	{
			return view('error', ['text' => "Can't find the movie"]);   		
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
			return view('error', ['text' => 'Movie NOT found?']);
    	}
    }

}
