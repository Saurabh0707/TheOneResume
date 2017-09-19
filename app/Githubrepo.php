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
    protected $fillable = ['owner','name','html_url','clone_url','repo_created_at','repo_updated_at','repo_pushed_at','public_repos','no_of_commits','no_of_branches','no_of_pullrequests','no_of_contributors'];

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
