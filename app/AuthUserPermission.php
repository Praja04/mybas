<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthUserPermission extends Model
{
    protected $table = 'auth_user_permission';
    protected $fillable = ['user_id', 'permission_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function permission()
    {
        return $this->belongsTo(AuthPermission::class, 'permission_id');
    }
}
