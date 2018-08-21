<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Video extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'url','type_local', 'user_id',
    ];

    public function user(){
       return $this->belongsTo(User::class);
    }
}
