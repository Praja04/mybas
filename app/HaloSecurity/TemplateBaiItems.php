<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;

class TemplateBaiItems extends Model
{
    protected $table = "template_bai_items"; // menghubungkan ke dalam tabel template_bai_items

    protected $fillable = ['pertanyaan_introgasi', 'jawaban_introgasi'];
}
