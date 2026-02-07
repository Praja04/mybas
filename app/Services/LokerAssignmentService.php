<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class LokerAssignmentService
{
    public function assign(array $data)
    {
        $jkMap = [
            'pria' => 'L',
            'wanita' => 'P',
            'L' => 'L', // Laki-laki
            'P' => 'P', // Perempuan
        ];

        $jk = $jkMap[$data['jk']] ?? null;
        if (!$jk) {
            throw new \Exception('Jenis kelamin tidak valid');
        }

        $noLoker = (int) $data['no_loker'];
        $staff   = $data['staff'];

        $kodeRak = $jk === 'L' ? 'PB' : 'WB';
        $pairRak = $this->pairRak($kodeRak);
        $rakDicek = array_filter([$kodeRak, $pairRak]);

        DB::transaction(function () use (
            $data, $jk, $noLoker, $staff, $kodeRak, $pairRak, $rakDicek
        ) {
            // 1️⃣ cegah >1 loker aktif
            $hasActive = DB::table('loker_penghuni')
                ->where('nik', $data['nik'])
                ->where('is_active', 'Y')
                ->lockForUpdate()
                ->exists();

            if ($hasActive) {
                throw new \Exception("NIK {$data['nik']} sudah memiliki loker aktif.");
            }

            // 2️⃣ cek isi loker
            $rows = DB::table('loker_penghuni')
                ->select('staff', DB::raw('COUNT(DISTINCT nik) as cnt'))
                ->whereIn('kode_rak', $rakDicek)
                ->where('no_loker', $noLoker)
                ->where('is_active', 'Y')
                ->groupBy('staff')
                ->lockForUpdate()
                ->get();

            if ($rows->count() > 1) {
                throw new \Exception("Loker {$kodeRak}-{$noLoker} tercampur kategori.");
            }

            $existingType  = $rows->first()->staff ?? null;
            $existingCount = (int) ($rows->first()->cnt ?? 0);

            if ($existingType && $existingType !== $staff) {
                $staffLabel = ucwords(str_replace('_', ' ', $existingType));
                throw new \Exception("Loker {$kodeRak}-{$noLoker} sudah dipakai oleh {$staffLabel}.");
            }

            $maxCapacity = app(LokerCapacityService::class)
                ->resolveMaxCapacity($staff);

            if ($existingCount >= $maxCapacity) {
                throw new \Exception("Loker {$kodeRak}-{$noLoker} sudah penuh.");
            }

            $penghuniSebelumnya = DB::table('loker_user_transaksi')
                ->where('no_loker', $noLoker)
                ->where('kode_rak', $kodeRak)
                ->where('status', 'OUT')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->value('penghuni_sebelumnya');

            // 3️⃣ insert rak utama
            DB::table('loker_penghuni')->insert([
                'nik' => $data['nik'],
                'nama' => $data['nama'],
                'divisi' => $data['divisi'],
                'jk' => $jk,
                'kode_rak' => $kodeRak,
                'no_loker' => $noLoker,
                'staff' => $staff,
                'is_active' => 'Y',
                'tgl_masuk' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

                DB::table('loker_user_transaksi')->insert([
                    'nik' => $data['nik'],
                    'no_loker' => $noLoker,
                    'status' => 'IN',
                    'keterangan' => 'Tambah Penghuni Baru',
                    'nama_pengisi' => auth()->user()->name ?? '',
                    'tgl_pengisi' => now()->format('Y-m-d'),
                    'nik_pengisi' => auth()->user()->username ?? '',
                    'jam_pengisi' => now()->format('H:i:s'),
                    'pindah_to' => null,
                    'penghuni_sebelumnya' => $penghuniSebelumnya ?? null,
                    'alasan' => 'Tambah Penghuni Baru',
                    'kode_area' => $data['kode_area'] ?? '',
                    'kode_blok' => $data['kode_blok'] ?? '',
                    'kode_rak' => $kodeRak ?? '',
                ]);


            // 4️⃣ insert rak pasangan
            if ($pairRak) {
                DB::table('loker_penghuni')->insert([
                    'nik' => $data['nik'],
                    'nama' => $data['nama'],
                    'divisi' => $data['divisi'],
                    'jk' => $jk,
                    'kode_rak' => $pairRak,
                    'no_loker' => $noLoker,
                    'staff' => $staff,
                    'is_active' => 'Y',
                    'tgl_masuk' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('loker_user_transaksi')->insert([
                    'nik' => $data['nik'],
                    'no_loker' => $noLoker,
                    'status' => 'IN',
                    'keterangan' => 'Tambah Penghuni Baru',
                    'nama_pengisi' => auth()->user()->name ?? '',
                    'tgl_pengisi' => now()->format('Y-m-d'),
                    'nik_pengisi' => auth()->user()->username ?? '',
                    'jam_pengisi' => now()->format('H:i:s'),
                    'pindah_to' => null,
                    'penghuni_sebelumnya' => $penghuniSebelumnya ?? null,
                    'alasan' => 'Tambah Penghuni Baru',
                    'kode_area' => $data['kode_area'] ?? '',
                    'kode_blok' => $data['kode_blok'] ?? '',
                    'kode_rak' => $pairRak ?? '',
                ]);

            }
        });
    }

    private function pairRak(string $kodeRak): ?string
    {
        return [
            'PB' => 'PS',
            'WB' => 'WS',
        ][$kodeRak] ?? null;
    }
}
