<?php

namespace App\Http\Controllers\PosSecurity\Ajax\Security;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\GaDataSecurity;
use Exception;
use Illuminate\Validation\ValidationException;
use Throwable;

class SecurityFormAjax extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nik' => 'required|string|max:150|unique:ga_data_security,nik',
                'nama_security' => 'required|string|max:50',
                'nomor_kartu' => 'required|string|max:50|unique:ga_data_security,nomor_kartu',
                'foto' => 'nullable|image|max:1024',
            ], [
                'nik.unique' => 'NIK sudah terdaftar.',
                'nomor_kartu.unique' => 'Nomor kartu sudah terdaftar.',
                'foto.image' => 'File foto harus berupa gambar.',
                'foto.max' => 'Ukuran foto maksimal 1MB.',
            ]);

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('security', 'public');
            }

            GaDataSecurity::create([
                'nik' => $validated['nik'],
                'nama_security' => $validated['nama_security'],
                'nomor_kartu' => $validated['nomor_kartu'],
                'foto' => $fotoPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data security berhasil disimpan',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $data = GaDataSecurity::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $security = GaDataSecurity::findOrFail($id);

            $validated = $request->validate(
                [
                    'nik' => 'required|string|max:150|unique:ga_data_security,nik,' . $id,
                    'nama_security' => 'required|string|max:50',
                    'nomor_kartu' => 'required|string|max:50|unique:ga_data_security,nomor_kartu,' . $id,
                    'foto' => 'nullable|image|max:1024',
                ],
                [
                    'nik.unique' => 'NIK sudah terdaftar.',
                    'nomor_kartu.unique' => 'Nomor kartu sudah terdaftar.',
                    'foto.image' => 'File foto harus berupa gambar.',
                    'foto.max' => 'Ukuran foto maksimal 1MB.',
                ]
            );

            $dataUpdate = [
                'nik' => $validated['nik'],
                'nama_security' => $validated['nama_security'],
                'nomor_kartu' => $validated['nomor_kartu'],
            ];

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('security', 'public');
                $dataUpdate['foto'] = $fotoPath;
            }

            $security->update($dataUpdate);

            return response()->json([
                'success' => true,
                'message' => 'Data security berhasil diperbarui'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat update data'
            ], 500);
        }
    }


    public function delete($id)
    {
        try {
            $security = GaDataSecurity::findOrFail($id);

            $security->update([
                'status' => 'inactive'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data security berhasil di-nonaktifkan.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menonaktifkan security: ' . $e->getMessage()
            ], 500);
        }
    }
}
