<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $fillable = ['user_id', 'college', 'degree', 'stream', 'type', 'from', 'to', 'marks'];

    public function user()
	{
    	return $this->belongsTo('App\User');
	}
}
