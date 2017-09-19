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
    protected $fillable = ['username','html_url','name','company','location','user_created_at','user_updated_at','public_repos','public_gists'];

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
