<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TChecklistApar extends Model
{
    public $table = 't_checklist_apar';
    protected $fillable = [
        'asset_id',
        'hanger',
        'tekanan',
        's_pin',
        'selang',
        'keterangan',
        'expired_tabung',
        'expired_date',
        'check_time',
        'check_by',
        'pictures'
    ];
}
