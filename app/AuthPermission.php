<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthPermission extends Model
{
    protected $table    = 'auth_permission';
    protected $fillable = ['name', 'codename'];

    public function groups()
    {
        return $this->belongsToMany(AuthGroup::class, 'auth_group_permission', 'permission_id', 'group_id');
    }
}
