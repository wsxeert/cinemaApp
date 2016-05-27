<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Movie extends Eloquent
{
	//protected $connection = 'mongodb';
    protected $collection = 'movies';

}