<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\AuthGroup;
use App\AuthPermission;
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
        'auth_group_id',
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

    /**
     * Permission yang langsung attach ke user (di luar group).
     */
    public function directPermissions()
    {
        return $this->belongsToMany(AuthPermission::class, 'auth_user_permission', 'user_id', 'permission_id');
    }

    /**
     * Gabungan permission dari group + direct user-specific.
     */
    public function getAllPermissionCodenames(): array
    {
        $codenames = [];
        if ($this->auth_group_id) {
            $group = AuthGroup::find($this->auth_group_id);
            if ($group) {
                $codenames = $group->permissions()->pluck('codename')->toArray();
            }
        }
        $direct = $this->directPermissions()->pluck('codename')->toArray();
        return array_values(array_unique(array_merge($codenames, $direct)));
    }

    /**
     * Cek apakah user punya permission langsung (direct, via auth_user_permission)
     * dengan codename tertentu.
     */
    public function hasDirectPermission(string $permission): bool
    {
        return $this->directPermissions()->where('codename', $permission)->exists();
    }

    /**
     * Ambil NAMES permission user (group + direct) yang codename-nya ber-prefix
     * tertentu. Return array nama permission (bukan codename) supaya bisa di-match
     * dengan nilai Sub Departmen / Departmen di database.
     */
    public function getPrefixPermission(string $prefix): array
    {
        $codenames = array_values(array_filter(
            $this->getAllPermissionCodenames(),
            fn (string $codename) => str_starts_with($codename, $prefix)
        ));

        if (empty($codenames)) {
            return [];
        }

        return AuthPermission::whereIn('codename', $codenames)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
