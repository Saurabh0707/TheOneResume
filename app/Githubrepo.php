<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Githubrepo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protected $fillable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function repobranches()
    {
        return $this->hasMany('App\Repobranche');
    }
    public function repocontributors()
    {
        return $this->hasMany('App\Repocontributor');
    }
    public function repolangs()
    {
        return $this->hasMany('App\Repolang');
    }
    public function repocommits()
    {
        return $this->hasMany('App\Repocommit');
    }
    public function githubuser()
	{
    	return $this->belongsTo('App\Githubuser');
	}
}
