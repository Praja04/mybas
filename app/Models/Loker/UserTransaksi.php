<?php
namespace App\Models\Loker;

use Illuminate\Database\Eloquent\Model;

class UserTransaksi extends Model
{
    // Nama tabel yang digunakan oleh model ini
    protected $table = 'loker_transaksi';

    // Nonaktifkan fitur timestamp (created_at dan updated_at)
    public $timestamps = false;

    // Kolom yang tidak boleh diisi secara massal
    protected $guarded = ['id'];

    protected $dates = [
        'created_at',
    ];
}
