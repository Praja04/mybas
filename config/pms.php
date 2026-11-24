<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PMS ACCESS OTP (HARDCODE)
    |--------------------------------------------------------------------------
    |
    | Gate OTP untuk area PMS yang sensitif (skor IPP/PK).
    |
    | Scope OTP:
    |   - /hr/pms-total-scoring              -> manager + admin
    |   - /hr/ipp-approval                   -> admin
    |   - /hr/pk-assessment-hr              -> admin
    |   - /hr/ipp-user-approval detail           -> manager
    |   - /hr/ipp-user-approval export           -> manager + Associate Manager
    |   - /hr/pk-assessment (leader/export/detail) -> manager
    |
    | BYPASS:
    |   Ubah 'enabled' menjadi false untuk mematikan gate OTP.
    |
    | DURASI AKSES:
    |   Ubah 'ttl_minutes' untuk lama grant akses PMS (menit).
    |
    | Setelah mengubah file ini, jalankan: php artisan config:clear
    |
    */

    'access_otp' => [
        // false = matikan total gate OTP akses PMS.
        // Aktifkan kembali setelah users.email admin/manager PMS lengkap.
        'enabled' => true,

        // Lama grant akses setelah OTP berhasil diverifikasi (menit).
        'ttl_minutes' => 20,

        // Lama kode OTP berlaku (menit).
        'code_ttl_minutes' => 5,

        // Lama halaman challenge / request OTP boleh dipakai (menit).
        'request_ttl_minutes' => 30,

        // Maksimal percobaan verifikasi per kode.
        'max_attempts' => 5,

        // Jeda minimal antar kirim OTP (detik).
        'resend_cooldown_seconds' => 60,

        // Maksimal kirim OTP dalam satu window.
        'max_sends_per_window' => 3,

        // Panjang window pembatasan kirim OTP (menit).
        'send_window_minutes' => 15,

        // Mailer yang dipakai untuk mengirim OTP.
        'mailer' => 'smtp',

        'from_address' => 'pms@pt-bas-id.com',
        'from_name' => 'PMS PT Bumi Alam Segar',
    ],

    /*
    |--------------------------------------------------------------------------
    | PMS PA SCALE FINAL ENCRYPTION (HARDCODE)
    |--------------------------------------------------------------------------
    |
    | Key AES-256-CBC (32 byte, format base64) untuk enkripsi kolom
    | hr_pms_pascore_final.pa_scale_final.
    |
    | PENTING:
    |   - Jangan ganti key setelah ada data terenkripsi. Data lama tidak bisa
    |     didekripsi dengan key yang berbeda.
    |   - Setelah mengubah file ini, jalankan: php artisan config:clear
    |
    */

    'pa_scale_final_encryption' => [
        'secret_key' => 'base64:SwxCUX3zOiMigHIWJUxjV4YcEGMwRPR1YHjILaxhUPw=',
    ],

];
