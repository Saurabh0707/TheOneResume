<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = ['user_id', 'achievement'];

    public function user()
	{
    	return $this->belongsTo('App\User');
	}
}
