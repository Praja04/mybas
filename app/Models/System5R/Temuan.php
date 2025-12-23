<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class Temuan extends Model
{
   protected $table = '5r_temuan';

   protected $primaryKey = 'id_temuan';

   protected $fillable = [
      'id_temuan',
      'id_pertanyaan',
      'id_jawaban',
      'id_periode',
      'id_area',
      'foto',
      'deskripsi',
      'created_at',
      'created_by'
   ];

   public function area()
   {
      return $this->belongsTo(MasterArea::class, 'id_area', 'id_area');
   }

   public function periode()
   {
      return $this->belongsTo(Periode::class, 'id_periode', 'id_periode');
   }

   public function pertanyaan()
   {
      return $this->belongsTo(MasterPertanyaan::class, 'id_pertanyaan', 'id_pertanyaan');
   }

   public function jawaban()
   {
      return $this->belongsTo(Jawaban::class, 'id_jawaban', 'id');
   }
}
