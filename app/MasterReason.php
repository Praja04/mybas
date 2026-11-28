<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterReason extends Model
{
    protected $table = 'master_reason_s2';

    // protected $guarded = [];

    protected $fillable = [
        'tipe',
        'kode_reason',
        'nama_reason',
        'is_active',
    ];
}
