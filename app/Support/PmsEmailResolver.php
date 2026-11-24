<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Resolusi email notifikasi PMS dari tabel users.
 *
 * Sumber tunggal: users.email. Pencarian utama lewat users.nik, dengan
 * fallback users.username untuk akun lama yang kolom nik-nya belum terisi.
 */
class PmsEmailResolver
{
    /**
     * @param  array<int, string>  $niks
     * @return array<string, string> key = NIK uppercase
     */
    public static function mapByNik(array $niks): array
    {
        $keys = [];
        foreach ($niks as $nik) {
            $key = strtoupper(trim((string) $nik));
            if ($key !== '') {
                $keys[$key] = $key;
            }
        }

        if (!$keys) {
            return [];
        }

        $out = [];

        foreach (DB::table('users')
                     ->whereIn('nik', array_values($keys))
                     ->get(['nik', 'email']) as $row) {
            $email = trim((string) $row->email);
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                $out[strtoupper(trim((string) $row->nik))] = $email;
            }
        }

        $missing = array_diff_key($keys, $out);
        if ($missing) {
            foreach (DB::table('users')
                         ->whereIn('username', array_values($missing))
                         ->get(['username', 'email']) as $row) {
                $email = trim((string) $row->email);
                if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                    $out[strtoupper(trim((string) $row->username))] = $email;
                }
            }
        }

        return $out;
    }

    public static function forNik(?string $nik): ?string
    {
        $key = strtoupper(trim((string) $nik));
        if ($key === '') {
            return null;
        }

        return static::mapByNik([$key])[$key] ?? null;
    }
}
