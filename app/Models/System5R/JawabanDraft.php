<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class JawabanDraft extends Model
{
   protected $table = '5r_jawaban_draft';

   protected $fillable = [
      'id_group',
      'id_periode',
      'nik_juri',
      'draft_data'
   ];

   protected $casts = [
      'draft_data' => 'array'
   ];
}
