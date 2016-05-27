<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Theater extends Eloquent
{
	//protected $connection = 'mongodb';
    protected $collection = 'theaters';
   
}
