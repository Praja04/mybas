<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthGroupPermission extends Model
{
    protected $table = 'auth_group_permission';
    protected $fillable = ['group_id', 'permission_id'];
}