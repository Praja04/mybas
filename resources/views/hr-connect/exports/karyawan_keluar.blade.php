<table style="border: 1px solid #000000;">
    <thead>
        <tr>
            <th style="font-weight: bold; background-color: #d9d9d9; border: 1px solid #000000; width: 25px;">Nama Lengkap</th>
            <th style="font-weight: bold; background-color: #d9d9d9; border: 1px solid #000000; width: 15px;">NIK</th>
            <th style="font-weight: bold; background-color: #d9d9d9; border: 1px solid #000000; width: 15px;">Kode Divisi</th>
            <th style="font-weight: bold; background-color: #d9d9d9; border: 1px solid #000000; width: 15px;">Kode Bagian</th>
            <th style="font-weight: bold; background-color: #d9d9d9; border: 1px solid #000000; width: 15px;">Kode Admin</th>
            <th style="font-weight: bold; background-color: #d9d9d9; border: 1px solid #000000; width: 30px;">Alasan Keluar (GA)</th>
            <th style="font-weight: bold; background-color: #d9d9d9; border: 1px solid #000000; width: 15px;">Tanggal Keluar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($karyawanCollection as $karyawan)
            <tr>
                <td style="border: 1px solid #000000;">{{ $karyawan['nama'] ?? '-' }}</td>
                <td style="border: 1px solid #000000;">'{{ $karyawan['nik'] ?? '-' }}</td>
                <td style="border: 1px solid #000000;">{{ $karyawan['kode_divisi'] ?? '-' }}</td>
                <td style="border: 1px solid #000000;">{{ $karyawan['kode_bagian'] ?? '-' }}</td>
                <td style="border: 1px solid #000000;">{{ $karyawan['kode_admin'] ?? '-' }}</td>
                <td style="border: 1px solid #000000;">{{ $karyawan['alasan_keluar'] }}</td>
                <td style="border: 1px solid #000000;">
                    {{ !empty($karyawan['tanggal_keluar']) ? \Carbon\Carbon::parse($karyawan['tanggal_keluar'])->format('d/m/Y') : '-' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
