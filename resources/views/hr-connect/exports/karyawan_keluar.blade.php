<!-- resources/views/exports/karyawan_baru.blade.php -->

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIK</th>
            <th>Kode Divisi</th>
            <th>Kode Bagian</th>
            <th>Kode Admin</th>
            <th>Alasan Keluar</th>
            <th>Tanggal Keluar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($karyawanCollection as $karyawan)
            <tr>
                <td>{{ $karyawan['nama'] }}</td>
                <td>{{ $karyawan['nik'] }}</td>
                <td>{{ $karyawan['kode_divisi'] }}</td>
                <td>{{ $karyawan['kode_bagian'] }}</td>
                <td>{{ $karyawan['kode_admin'] }}</td>
                <td>{{ $karyawan['alasan_keluar'] }}</td>
                <td>{{ $karyawan['tanggal_keluar'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
