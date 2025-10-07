<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use App\PKWKaryawan;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;

class PKWKaryawanExportBPJSTKSheet1 implements WithColumnFormatting, FromArray, WithTitle
{
    public function array(): array
    {
        $data = [];
        $data[] = [''];
        $data[] = [''];
        $data[] = ['Langkah Penggunaan Sheet "Data TK Baru"'];
        $data[] = ['Pengisian Column', 'Petunjuk'];
        $data[] = ['NIK', 'Berisi nomor identitas dari tenaga kerja. Wajib diisi. Sistem akan melakukan pengecekan dengan data Adminduk jika jenis identitas adalah KTP.'];
        $data[] = ['NAMA', 'Berisi nama depan/nama lengkap dari tenaga kerja. Wajib diisi.'];
        $data[] = ['TGL_LAHIR', 'Berisi tgl_lahir dari tenaga kerja, diisi dengan format dd-mm-yyyy contoh : 31-12-1992. Wajib diisi.'];
        $data[] = ['JENIS_IDENTITAS', 'Berisi jenis identitas dari tenaga kerja. pilihan ada 2 yaitu PASSPORT atau KTP. contoh : PASSPORT'];
        $data[] = ['MASA_LAKU_IDENTITAS', 'Berisi masa berlaku identitas tenaga kerja, diisi dengan format dd-mm-yyyy contoh : 31-12-1992.  Wajib diisi'];
        $data[] = ['JENIS_KELAMIN', 'Berisi jenis kelamin tenaga kerja, diisi dengan inisial L atau P.'];
        $data[] = ['SURAT_MENYURAT_KE', 'Berisi surat menyurat dikirim ke, diisi dengan initial S atau E. Keterangan : S (Alamat) , E (Email) '];
        $data[] = ['STATUS_KAWIN', 'Berisi status kawin dari tenaga kerja, diisi dengan initial Y atau T. Keterangan : Y (KAWIN) , T (BELUM KAWIN) '];
        $data[] = ['GOLONGAN_DARAH', 'Berisi golongan darah dari tenaga kerja, diisi dengan golongan A, B, AB, O.'];
        $data[] = ['KODE_NEGARA', 'Berisi kode negara dari tenaga kerja, diisi dengan kode negara contoh : ID , keterangan : ID (INDONESIA).'];
        $data[] = ['LOKASI_PEKERJAAN', 'Berisi Kode Lokasi Pekerjaan dari tenaga kerja, untuk daftar kode Pekerjaan terdapat pada sheet LOKASI_PEKERJAAN.'];
        $data[] = [''];
        $data[] = ['Untuk pengisian data tenaga kerja terdapat di sheet data_tk_baru '];

        return $data;
    }

    public function columnFormats(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Petunjuk Pengisian';
    }
}
