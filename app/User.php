<?php
namespace App;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable
{
    use Notifiable, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function githubusers()
    {
        return $this->hasMany('App\Githubuser');
    }
    public function skills()
    {
        return $this->hasMany('App\Skill');
    }
    public function educations()
    {
        return $this->hasMany('App\Education');
    }
    public function works()
    {
        return $this->hasMany('App\Work');
    }
    public function achievements()
    {
        return $this->hasMany('App\Achievement');
    }
}