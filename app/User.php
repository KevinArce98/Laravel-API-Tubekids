<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Video;
use App\Kid;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    const VERIFICADO = 1;
    const NO_VERIFICADO = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'lastname','email', 'password', 'country', 'verified','date_birthday', 'verification_token',
    ];

    /* Mutadores */

    public function setNameAttribute($value){
        $this->attributes['name'] = strtolower($value);
    }

    public function setEmailAttribute($value){
        $this->attributes['email'] = strtolower($value);
    }

    /* Accesores */
    public function getNameAttribute($value){
        return ucwords($value);
    }

    public function getLastnameAttribute($value){
        return ucwords($value);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'verification_token'
    ];

    public function verified(){
        return $this->verified == User::VERIFICADO;
    }

    public function not_verified()
    {
        return $this->verified == User::NO_VERIFICADO;
    }

    public static function formatDateToCarbon($date){
        $date = Carbon::createFromFormat('d/m/Y', $date);
        return $date;
    }
    public static function formatDateToSQL($date){
       $date = "$date->year-$date->month-$date->day";
        return $date;
    }

    public static function verifyDate(Carbon $date_birthday){
        $today = Carbon::now();
        return $date_birthday->diffInYears($today);  
    }

    public function videos(){
        return $this->hasMany(Video::class);
    }
    public function kids(){
        return $this->hasMany(Kid::class);
    }
}
