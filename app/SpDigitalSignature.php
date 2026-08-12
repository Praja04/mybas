<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SpDigitalSignature extends Model
{
    protected $table = 'sp_digital_signatures';

    protected $fillable = [
        'user_id',
        'role',
        'nama_jabatan',
        'signature_path',
        'is_active',
        'uploaded_at',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'uploaded_at' => 'datetime',
    ];

    // ─── Relations ──────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Accessors ───────────────────────────────────────────────────────────────

    /**
     * URL publik ke gambar TTD (untuk tampilan di web)
     */
    public function getSignatureUrlAttribute()
    {
        if (!$this->signature_path) return null;
        return Storage::disk('public')->url($this->signature_path);
    }

    /**
     * Base64-encoded data URI gambar TTD (untuk embed di PDF DomPDF)
     */
    public function getSignatureBase64Attribute()
    {
        if (!$this->signature_path) return null;

        try {
            $fullPath = Storage::disk('public')->path($this->signature_path);
            if (!file_exists($fullPath)) return null;

            $content  = file_get_contents($fullPath);
            $ext      = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $mimeMap  = [
                'png'  => 'image/png',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'svg'  => 'image/svg+xml',
            ];
            $mime = $mimeMap[$ext] ?? 'image/png';

            return 'data:' . $mime . ';base64,' . base64_encode($content);
        } catch (\Exception $e) {
            logger()->error('Gagal baca TTD digital: ' . $e->getMessage());
            return null;
        }
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    /**
     * Ambil TTD aktif milik user tertentu.
     */
    public static function getForUser($userId)
    {
        return static::where('user_id', $userId)->where('is_active', true)->first();
    }

    /**
     * Ambil TTD IR Head aktif (untuk kolom "Dibuat" pada PDF).
     */
    public static function getIrHead()
    {
        return static::where('role', 'ir_head')->where('is_active', true)->first();
    }

    /**
     * Ambil TTD Dept Head berdasarkan user_id Dept Head.
     */
    public static function getDeptHead($userId)
    {
        return static::where('user_id', $userId)->where('is_active', true)->first();
    }
}
