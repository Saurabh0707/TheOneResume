<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RepoPR extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["state","title","body","assignee","creator", "open_issues","closed_issues","created_at", "updated_at","closed_at","merged_at"];

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
