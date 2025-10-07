<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = '5r_jawaban';
    protected $guarded = [];

    public function pertanyaan()
    {
        return $this->belongsTo(MasterPertanyaan::class, 'id_pertanyaan');
    }
}
