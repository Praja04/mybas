<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class Nvr extends Model
{
    protected $connection = 'ite';
    protected $fillable = [
    	'ip_address',
    	'jenis',
    	'firmware_version',
    	'jumlah_channel',
    	'jumlah_slot_harddisk',
    	'kapasitas_harddisk',
    	'username',
    	'password'
	];
	
    public function cameras()
    {
        return $this->hasMany('App\Models\Ite\Camera', 'nvr_id');
    }
}
