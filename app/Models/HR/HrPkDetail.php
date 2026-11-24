<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPkDetail extends Model
{
    protected $table = 'hr_pk_detail';

    protected $fillable = [
        'hr_pk_id',
        'section',
        'category',
        'level_group',
        'no_urut',
        'area',
        'competency_behavior',
        'key_behavior',
        'kriteria_1',
        'kriteria_2',
        'kriteria_3',
        'kriteria_4',
        'kriteria_5',
    ];

    public function itemLabel(): string
    {
        $text = trim((string) ($this->competency_behavior !== null && $this->competency_behavior !== '' ? $this->competency_behavior : $this->area));
        $first = $text !== '' ? trim(preg_split('/\r\n|\r|\n/', $text)[0]) : '';
        return trim($this->no_urut . '. ' . $first, '. ');
    }

    public function pk()
    {
        return $this->belongsTo(HrPk::class, 'hr_pk_id');
    }
}