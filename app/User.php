<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'public_key', 'data'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function chats()
    {
        return $this->belongsToMany(Chat::class)
            ->using(ChatUser::class)
            ->withPivot([ 'key', 'nickname' ])
            ->withTimeStamps();
    }

    public function getDataAttribute($value)
    {
        return json_decode($value);
    }
}
