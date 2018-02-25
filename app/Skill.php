<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    // public function skills()
    // {
    //     return $this->hasMany('App\Githubrepo');
    // }
    protected $fillable = ['user_id','skill'];

   public function user()
	{
    	return $this->belongsTo('App\User');
	}
}
