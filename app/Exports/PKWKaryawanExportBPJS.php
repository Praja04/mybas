<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use App\PKWKaryawan;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PKWKaryawanExportBPJS implements WithColumnFormatting, FromArray
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function array(): array
    {
        $data = [];
        $data[] = ['FORM MUTASI DATA PESERTA'];
        $data[] = ['JENIS PESERTA'];
        $data[] = ['NAMA INSTANSI/BADAN/PERUSAHAAN'];
        $data[] = ['KODE VIRTUAL ACCOUNT'];
        $data[] = ['BANK TEMPAT PEMBAYARAN IURAN'];
        $data[] = ['TANGGAl REGISTRASI'];
        $data[] = ['NOMOR PKS'];
        $data[] = ['KODE PKS'];
        $data[] = ['MASA BERLAKU'];
        $data[] = ['KODE TANGGUNGAN'];
        $data[] = ['KODE KC'];
        $data[] = ['KODE DATI2'];
        $data[] = [''];
        $data[] = ['','','','','','','','PISA'];
        
        $data[] = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Kode HubKel',
            'Tgl Lahir',
            '',
            'Jenis Kelamin',
            'Status Kawin',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Status',
            'Kelas Rawat',
            '',
            '',
            'Kewarga Negaraan',
            'Asuransi Lainnya',
            '',
            '',
            ''
        ];
        $data[] = [
            'No', // A
            'No Kartu BPJS Kesehatan', // B
            'Jenis Mutasi', // C
            'Tgl Aktif Berlaku Mutasi', // D
            'No KK', // E
            'NIK/KITAS/KITAP', // F
            'Nama Lengkap', // G
            '1 = Peserta 2 = Suami 3 = Istri 4 = Anak 5 = Tambahan', // H
            'Tempat Lahir', // I
            'mm/dd/yyyy', // J
            '1=L  2=P', // K
            '0=TD, 1=B, 2=K,3=C', // L
            'Alamat Tempat Tinggal', // M
            'RT', // N
            'RW', // O
            'Kode Pos', // P
            'Kode Kecamatan', // Q
            'Nama Kecamatan', // R
            'Kode Desa', // S
            'Nama Desa', // T
            'Kode Faskes Tk.I', // U
            'Nama Faskes Tk.I', // V
            'Kode Faskes Dokter Gigi', // W
            'Nama Faskes Dokter Gigi', // X
            'Nomor Telepon Peserta', // Y
            'Email', // Z
            'NIP', // AA
            'Jabatan', // AB
            '1=Tetap, 2=Kontrak, 3=Paruh waktu 4=Penerima Pensiun', // AC
            '1=Kelas I, 2=Kelas II, 3=Kelas III', // AD
            'TMT Kerja (Kary. Aktif)', // AE
            'Gaji Pokok + Tunj. Tetap (Kary. Aktif)', // AF
            '1=WNI, 2=WNA', // AG
            'No. Kartu Asuransi', // AH
            'Nama Asuransi', // AI
            'No. NPWP', // AJ
            'No Passport' // AK
        ];
        $data[] = [
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '10',
            '11',
            '12',
            '13',
            '14',
            '15',
            '16',
            '17',
            '18',
            '19',
            '20',
            '21',
            '22',
            '23',
            '24',
            '25',
            '26',
            '27',
            '28',
            '29',
            '30',
            '31',
            '32',
            '33',
            '34',
            '35',
            '36',
            '37'
        ];

        $employees = PKWKaryawan::whereIn('id', explode(',', $this->ids))->get();
        foreach($employees as $key => $employee) {
            $data[] = [
                $key+1,
                $employee->nomor_kartu_bpjs_kesehatan,
                '',
                '',
                $employee->no_kk,
                str_replace( "'"," ", $employee->nik_ktp."'" ),
                $employee->nama,
                '',
                $employee->tempat_lahir,
                explode('-', $employee->tanggal_lahir)[1].'/'.explode('-', $employee->tanggal_lahir)[2].'/'.explode('-', $employee->tanggal_lahir)[0],
                $employee->jenis_kelamin,
                '',
                $employee->alamat_sekarang.', Kel. '.ucfirst(strtolower($employee->sekarang_desa->name)).', Kec. '.ucfirst(strtolower($employee->sekarang_kecamatan->name)).', Kota. '.ucfirst(strtolower($employee->sekarang_kota->name)).', Provinsi '.ucfirst(strtolower($employee->sekarang_provinsi->name)),
                '',
                '',
                '',
                '',
                $employee->sekarang_kecamatan->name,
                '',
                $employee->sekarang_desa->name,
                '',
                '',
                '',
                '',
                '',
                '',
                $employee->nik,
                $employee->jabatan->nama_jabatan,
                '',
                '',
                '',
                '',
                '1',
                '',
                '',
                $employee->no_npwp,
                ''
            ];
        }

        return $data;
    }

    public function columnFormats(): array
    {
        return [
            'B' => '#0',
            'E' => '#0',
            'F' => '#0',
            'Y' => '#0',
            'AJ' => '#0',
        ];
    }
}
