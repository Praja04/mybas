<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Department;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'position',
        'status',
        'username',
        'password',
        'dept_id',
        'email',
    ];

    public function group()
    {
        return $this->belongsTo('App\AuthGroup', 'auth_group_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Department', 'dept_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
