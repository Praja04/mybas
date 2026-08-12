<?php

namespace App\Http\Controllers;

use App\SpDigitalSignature;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DigitalSignatureController extends Controller
{
    private const ALLOWED_ROLES = [
        'sp_pelanggaran_dh'       => 'dept_head',
        'sp_pelanggaran_approval_dh' => 'dept_head',
        'sp_pelanggaran_ir_staff' => 'ir_staff',
        'sp_pelanggaran_ir_head'  => 'ir_head',
    ];

    private const ROLE_LABELS = [
        'dept_head' => 'Dept Head / Manager',
        'ir_staff'  => 'IR Staff',
        'ir_head'   => 'IR Head',
    ];

    /**
     * Deteksi role SP dari permissions user yang sedang login.
     */
    private function detectUserSpRole($userId = null)
    {
        $userId = $userId ?: (Auth::id() ?: session('user_id'));
        $user   = User::find($userId);
        if (!$user) return null;

        $perms = [];
        if (method_exists($user, 'directPermissions')) {
            $perms = array_merge($perms, $user->directPermissions->pluck('codename')->toArray());
        }
        if (method_exists($user, 'group') && $user->group && method_exists($user->group, 'permissions')) {
            $perms = array_merge($perms, $user->group->permissions->pluck('codename')->toArray());
        }

        foreach (self::ALLOWED_ROLES as $perm => $role) {
            if (in_array($perm, $perms)) {
                return $role;
            }
        }

        // superuser / admin: pilih role via param
        return null;
    }

    /**
     * GET /ttd-digital
     * Halaman kelola TTD digital milik user yang login.
     */
    public function index(Request $request)
    {
        if (!Auth::check() && !session('login')) {
            return redirect('/login');
        }

        $user       = Auth::user();
        $permissions = view()->shared('permissions') ?: [];
        $spRole     = $this->detectUserSpRole();

        // Load semua TTD yang ada di sistem (untuk superadmin/IR Head melihat semua)
        $isIrHead   = in_array('sp_pelanggaran_ir_head', $permissions);
        $isAdmin    = $user && in_array($user->user_role ?? '', ['superadmin', 'admin']);

        if ($isIrHead || $isAdmin) {
            $allSignatures = SpDigitalSignature::with('user')->orderBy('role')->orderBy('updated_at', 'desc')->get();
        } else {
            $allSignatures = collect();
        }

        // TTD milik user yang login
        $mySignature = SpDigitalSignature::getForUser(Auth::id());

        return view('sp_pelanggaran.ttd_digital', compact(
            'mySignature',
            'allSignatures',
            'spRole',
            'isIrHead',
            'isAdmin'
        ));
    }

    /**
     * POST /ttd-digital
     * Upload / update TTD baru.
     */
    public function store(Request $request)
    {
        if (!Auth::check() && !session('login')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'signature_image'  => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'signature_base64' => 'nullable|string',
            'nama_jabatan'     => 'nullable|string|max:150',
            'role_override'    => 'nullable|string|in:dept_head,ir_staff,ir_head',
        ]);

        if (!$request->hasFile('signature_image') && !$request->filled('signature_base64')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Silakan buat tanda tangan pada Canvas atau upload file gambar TTD.'
            ], 422);
        }

        $userId  = Auth::id();
        $spRole  = $this->detectUserSpRole($userId);

        // IR Head / admin bisa override role untuk user lain
        $permissions = view()->shared('permissions') ?: [];
        $isIrHead    = in_array('sp_pelanggaran_ir_head', $permissions);

        if ($request->filled('role_override') && ($isIrHead || ($user = Auth::user()) && in_array($user->user_role ?? '', ['superadmin', 'admin']))) {
            $spRole = $request->role_override;
        }

        if (!$spRole) {
            $spRole = 'dept_head'; // default fallback
        }

        $filename = 'ttd_' . $userId . '_' . time() . '.png';
        $path     = 'sp_ttd_digital/' . $filename;

        if ($request->filled('signature_base64')) {
            $base64Data = $request->input('signature_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $data = substr($base64Data, strpos($base64Data, ',') + 1);
                $data = base64_decode($data);
                if ($data === false) {
                    return response()->json(['status' => 'error', 'message' => 'Format gambar canvas tidak valid.'], 422);
                }
                Storage::disk('public')->put($path, $data);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Format Base64 gambar tidak valid.'], 422);
            }
        } elseif ($request->hasFile('signature_image')) {
            $file     = $request->file('signature_image');
            $filename = 'ttd_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('sp_ttd_digital', $filename, 'public');
        }

        // Hapus TTD lama jika ada
        $existing = SpDigitalSignature::where('user_id', $userId)->first();
        if ($existing && $existing->signature_path && Storage::disk('public')->exists($existing->signature_path)) {
            Storage::disk('public')->delete($existing->signature_path);
        }

        // Simpan atau update
        $namaJabatan = $request->input('nama_jabatan');
        if (!$namaJabatan) {
            $namaJabatan = self::ROLE_LABELS[$spRole] ?? $spRole;
        }

        SpDigitalSignature::updateOrCreate(
            ['user_id' => $userId],
            [
                'role'           => $spRole,
                'nama_jabatan'   => $namaJabatan,
                'signature_path' => $path,
                'is_active'      => true,
                'uploaded_at'    => now(),
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Tanda tangan digital berhasil disimpan dan akan digunakan pada semua Surat Peringatan.',
        ]);
    }

    /**
     * DELETE /ttd-digital/{id}
     * Hapus TTD.
     */
    public function destroy($id)
    {
        $sig = SpDigitalSignature::findOrFail($id);

        $userId = Auth::id();
        $permissions = view()->shared('permissions') ?: [];
        $isIrHead = in_array('sp_pelanggaran_ir_head', $permissions);
        $user = Auth::user();
        $isAdmin = $user && in_array($user->user_role ?? '', ['superadmin', 'admin']);

        // Hanya boleh hapus milik sendiri, kecuali IR Head / admin
        if ($sig->user_id !== $userId && !$isIrHead && !$isAdmin) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak berhak menghapus TTD ini.'], 403);
        }

        if ($sig->signature_path && Storage::disk('public')->exists($sig->signature_path)) {
            Storage::disk('public')->delete($sig->signature_path);
        }

        $sig->delete();

        return response()->json(['status' => 'success', 'message' => 'Tanda tangan digital berhasil dihapus.']);
    }

    /**
     * GET /ttd-digital/preview
     * Preview gambar TTD milik user yang login (untuk JS live preview).
     */
    public function preview()
    {
        $sig = SpDigitalSignature::getForUser(Auth::id());
        if (!$sig || !$sig->signature_url) {
            return response()->json(['status' => 'not_found', 'url' => null]);
        }
        return response()->json(['status' => 'found', 'url' => $sig->signature_url]);
    }
}
