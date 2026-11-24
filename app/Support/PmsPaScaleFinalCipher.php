<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PmsPaScaleFinalCipher
{
    /**
     * Prefix penanda ciphertext agar bisa dibedakan dari data plaintext lama
     * dan memudahkan rotasi format enkripsi di masa depan.
     */
    private const PREFIX = 'pmsenc:v1:';

    private const CIPHER = 'AES-256-CBC';

    private const KEY_LENGTH = 32;

    /** @var Encrypter|null */
    private static $encrypter;

    /** @var string|null */
    private static $keySignature;

    /**
     * Enkripsi nilai PA Scale Final. Null / string kosong disimpan sebagai null.
     *
     * @param  mixed  $value
     * @return string|null
     */
    public static function encrypt($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        return self::PREFIX . self::encrypter()->encryptString($value);
    }

    /**
     * Dekripsi nilai PA Scale Final. Nilai tanpa prefix dianggap plaintext lama
     * agar tetap terbaca selama masa transisi deploy.
     *
     * @param  mixed  $value
     * @return string|null
     */
    public static function decrypt($value)
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;
        if ($value === '') {
            return null;
        }

        if (!self::isEncrypted($value)) {
            return $value;
        }

        try {
            return self::encrypter()->decryptString(substr($value, strlen(self::PREFIX)));
        } catch (DecryptException $e) {
            Log::error('Gagal mendekripsi pa_scale_final PMS: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * @param  mixed  $value
     */
    public static function isEncrypted($value): bool
    {
        return is_string($value) && strpos($value, self::PREFIX) === 0;
    }

    private static function encrypter(): Encrypter
    {
        $key = (string) config('pms.pa_scale_final_encryption.secret_key', '');

        if (strpos($key, 'base64:') === 0) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded === false) {
                throw new RuntimeException('Secret key enkripsi pa_scale_final PMS bukan base64 yang valid.');
            }

            $key = $decoded;
        }

        if (strlen($key) !== self::KEY_LENGTH) {
            throw new RuntimeException('Secret key enkripsi pa_scale_final PMS harus 32 byte (AES-256-CBC).');
        }

        if (self::$encrypter === null || self::$keySignature !== $key) {
            self::$encrypter = new Encrypter($key, self::CIPHER);
            self::$keySignature = $key;
        }

        return self::$encrypter;
    }
}
