<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Repocommit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['sha','author','committer','message','commit_created_at','commit_updated_at' ];

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
