<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Repolang extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','lines'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function githubrepo()
	{
    	return $this->belongsTo('App\Githubrepo');
	}
}
