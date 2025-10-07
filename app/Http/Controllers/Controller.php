<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\AuthGroup;
use App\AuthGroupPermission;
use App\AuthPermission;
use App\User;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function permissionMember($permission)
    {
        $permission = AuthPermission::where('codename', $permission)->first();
        $authGroupPermission = AuthGroupPermission::where('permission_id', $permission->id)->get()->pluck('group_id')->toArray();

        $users = User::whereIn('auth_group_id', $authGroupPermission)->get();

        return $users;
    }

    public function permission($permission)
    {
        $permissions = AuthGroup::find(Auth::user()->auth_group_id)->permissions()->orderBy('name')->get();
        if($permissions->where('codename', $permission)->first() == null) {
            // Ini berarti ga boleh akses. Langsung Redirect ke halaman 403
            abort(403);
        }

    }

    public function myPermissions()
    {
        $permissions = AuthGroup::find(Auth::user()->auth_group_id)->permissions()->orderBy('name')->get()->pluck('codename');
        return $permissions;
    }

    public function generateUniqName()
    {
        return md5(uniqid(time(), true));
    }

    public function formatTanggal($date) {

        if($date == null) {
            return '-';
        }

        $year = explode('-', $date)[0];
        $month = explode('-', $date)[1];
        $date = explode('-', $date)[2];
        return $date . '/' . $month . '/' . $year;
    }

    function expired($expired_date) {
        return (strtotime($expired_date) - strtotime(date('Y-m-d'))) / 86400;
    }

    // Format penggunaan generateID(prefix, nama tabel, nama kolom)
    // Contoh penggunaan $kode = $this->generateID('O', 'obat', 'kode_obat');
    public function generateID($prefix, $tableName, $columnName, $aditionalWhere = null)
    {
        $prefixLength = strlen($prefix);

        $numberBefore = DB::table($tableName)
        ->selectRaw('SUBSTR('.$columnName.', '.($prefixLength+1).') as code')
        ->orderByRaw('CAST(SUBSTR('.$columnName.', '.($prefixLength+1).') AS SIGNED) desc')
        ->whereRaw('SUBSTR('.$columnName.',1, '.($prefixLength).') = \''.$prefix.'\'');
        
        if($aditionalWhere != null) {
            $numberBefore = $numberBefore->whereRaw($aditionalWhere);
        }

        $numberBefore = $numberBefore->first();

        if($numberBefore == null) {
            return $prefix.'00001';
        }

        $currentNumber = (int)$numberBefore->code+1;
        
        switch ($currentNumber) {
            case $currentNumber < 10:
                $currentCode = $prefix.'0000'.$currentNumber;
                break;
            case $currentNumber < 100:
                $currentCode = $prefix.'000'.$currentNumber;
                break;
            case $currentNumber < 1000:
                $currentCode = $prefix.'00'.$currentNumber;
                break;
            case $currentNumber < 10000:
                $currentCode = $prefix.'0'.$currentNumber;
                break;
            default:
                $currentCode = $prefix.$currentNumber;
                break;
        }

        return $currentCode;
    }
}
