<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Githubuser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name', 'email', 'password',
    // ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

   public function githubrepos()
    {
        return $this->hasMany('App\Githubrepo');
    }
   public function user()
	{
    	return $this->belongsTo('App\User');
	}
}
