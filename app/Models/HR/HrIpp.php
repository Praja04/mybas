<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIpp extends Model
{
    protected $table = 'hr_ipp';

    protected $fillable = [
        'periode',
        'dept',
        'position',
        'golongan',
        'template_type',
        'send_by_username',
    ];

    public function details()
    {
        return $this->hasMany(HrIppDetail::class, 'ipp_id')->orderBy('no_urut');
    }

    public function foremanRows()
    {
        return $this->hasMany(HrIppForeman::class, 'ipp_id')->orderBy('id');
    }

    public function isRubric(): bool
    {
        return trim((string) $this->template_type) === 'rubric';
    }

    public function otorisasi()
    {
        return $this->hasMany(HrIppOtorisasi::class, 'ipp_id');
    }
}
