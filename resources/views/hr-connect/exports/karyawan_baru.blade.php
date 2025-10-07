<!-- resources/views/exports/karyawan_baru.blade.php -->

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIK</th>
            <th>Tanggal Masuk</th>
            <th>Jenis Kelamin</th>
            <th>Kode Divisi</th>
            <th>Kode Bagian</th>
            <th>Kode Group</th>
            <th>Proses</th>
        </tr>
    </thead>
    <tbody>
        @foreach($karyawanCollection as $karyawan)
            <tr>
                <td>{{ $karyawan['nama'] }}</td>
                <td>{{ $karyawan['nik'] }}</td>
                <td>{{ $karyawan['tanggal_masuk'] }}</td>
                <td>{{ $karyawan['jenis_kelamin'] }}</td>
                <td>{{ $karyawan['kode_divisi'] }}</td>
                <td>{{ $karyawan['kode_bagian'] }}</td>
                <td>{{ $karyawan['kode_group'] }}</td>
                <td>{{ $karyawan['p_in'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
