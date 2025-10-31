<?php
// buat testing doang

// use Illuminate\Support\Facades\Route;
// use App\Mail\EDoc\EDocMail;
// use App\Mail\EDoc\RemainderMail;
// use Illuminate\Support\Facades\Mail;

// Route::get('/preview', function () {
//     $detail = [
//         'petugas' => 'Dodi',
//         'tanggal_kedatangan' => now(),
//         'nama_pt_pengirim' => 'PT PAS',
//         'nama_penerima' => 'Budi',
//         'jenis' => 'Barang',
//         'keterangan' => 'Dokumen invoice bulanan',
//     ];

//     $mail = new EDocMail(
//         'Yth. ' . $detail['nama_penerima'] . ', MyBAS mencatat adanya dokumen masuk (E-Document) yang ditujukan kepada Anda. Berikut detail informasinya:',
//         'E-DOCUMENT NOTIFICATION',
//         $detail
//     );

//     // return $mail->render();

//     Mail::to('')->send($mail);
//     return '✅ Email E-Document Notification berhasil dikirim.';
// });

// Route::get('/preview-return', function () {
//     $data = (object)[
//         'dept_penerima' => 'ITE',
//         'nama_penerima' => 'Damang',
//         'nama_pt_pengirim' => 'PT PAS',
//         'tanggal_kedatangan' => now(),
//         'jenis' => 'Dokumen',
//         'keterangan' => 'Invoice bulan Oktober 2025',
//     ];

//     $mail = new RemainderMail($data);

//     // return $mail->render();
//     Mail::to('')->send($mail);

//     return '✅ Email Reminder Pengambilan Dokumen berhasil dikirim.';
// });
