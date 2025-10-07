<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthGroup extends Model
{
    protected $table = 'auth_group';
    protected $fillable = ['name'];

    function permissions()
    {
        return $this->belongsToMany('App\AuthPermission', 'auth_group_permission', 'group_id', 'permission_id');
    }
}
