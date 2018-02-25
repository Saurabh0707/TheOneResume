<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
	protected $fillable = ['user_id', 'position', 'location', 'organisation', 'description', 'from','to'];

    public function user()
	{
    	return $this->belongsTo('App\User');
	}
}
