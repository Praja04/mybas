<?php

namespace App\Support;

use App\Models\HR\HrPmsEmployeeSnapshot;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Snapshot atribut karyawan per periode PMS.
 *
 * Dipakai agar history periode lama tidak ikut berubah ketika
 * hr_master_employee diperbarui (promosi / mutasi).
 *
 * Aturan:
 * - Data identitas & organisasi di-freeze saat pertama kali capture.
 * - Field yang belum tersedia saat capture (manager, atasan, jumlah bawahan,
 *   pendidikan) boleh diisi menyusul, tetapi tidak menimpa nilai yang sudah ada.
 */
class PmsEmployeeSnapshot
{
    public const SOURCE_IPP = 'ipp';
    public const SOURCE_PK = 'pk';
    public const SOURCE_PK_OTORISASI = 'pk_otorisasi';
    public const SOURCE_PK_FOREMAN = 'pk_foreman';
    public const SOURCE_BACKFILL = 'backfill';
    public const SOURCE_SIMULASI = 'simulasi';
    public const SOURCE_MANUAL = 'manual';

    /**
     * Field yang boleh diisi menyusul (fill-if-empty).
     */
    private const DEFERRED = [
        'manager_nik',
        'manager_nama',
        'associate_manager_nik',
        'associate_manager_nama',
        'jumlah_bawahan',
        'pendidikan',
    ];

    public static function groupOf($level): ?string
    {
        if ($level === null || $level === '') {
            return null;
        }

        $level = (int) $level;
        if ($level >= 6) {
            return '6-7';
        }
        if ($level >= 4) {
            return '4-5';
        }
        if ($level >= 2) {
            return '2-3';
        }

        return null;
    }

    /**
     * Normalisasi kunci periode: bila hanya ada satu tahun di string, pakai tahun itu.
     */
    public static function normalizePeriode(?string $periode): string
    {
        $periode = trim((string) $periode);
        if ($periode === '') {
            return '';
        }

        preg_match_all('/\d{4}/', $periode, $m);
        $years = array_values(array_unique($m[0] ?? []));
        if (count($years) === 1) {
            return $years[0];
        }

        return $periode;
    }

    /**
     * Semua periode PMS yang ada (IPP + PK), ternormalisasi dan terurut.
     *
     * @return array<int, string>
     */
    public static function periodeOptions(): array
    {
        $periodes = [];

        foreach (DB::table('hr_ipp')->distinct()->pluck('periode') as $p) {
            $n = static::normalizePeriode((string) $p);
            if ($n !== '') {
                $periodes[$n] = true;
            }
        }
        foreach (DB::table('hr_pk')->distinct()->pluck('periode') as $p) {
            $n = static::normalizePeriode((string) $p);
            if ($n !== '') {
                $periodes[$n] = true;
            }
        }

        $out = array_keys($periodes);
        sort($out);

        return $out;
    }

    /**
     * Ambil (capture) snapshot satu karyawan untuk satu periode.
     * Bila sudah ada, hanya field deferred yang mungkin diisi.
     */
    public static function capture(string $periode, string $nik, array $opts = []): ?HrPmsEmployeeSnapshot
    {
        $periode = static::normalizePeriode($periode);
        $nik = trim($nik);
        if ($periode === '' || $nik === '') {
            return null;
        }

        $existing = HrPmsEmployeeSnapshot::where('periode', $periode)->where('nik', $nik)->first();
        if ($existing) {
            static::fillDeferred($periode, $nik, $opts);

            return $existing;
        }

        $emp = DB::table('hr_master_employee')->where('NIK', $nik)->first([
            'NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section',
            'Level', 'Tgl Masuk', 'Tgl Lahir',
        ]);

        if (!$emp && !isset($opts['nama'])) {
            return null;
        }

        $level = $opts['level'] ?? ($emp->Level ?? null);
        $tanggalLahir = $emp->{'Tgl Lahir'} ?? null;

        try {
            return HrPmsEmployeeSnapshot::create([
                'periode' => $periode,
                'nik' => $nik,
                'nama' => $opts['nama'] ?? ($emp->Nama ?? null),
                'jabatan' => $opts['jabatan'] ?? ($emp->Jabatan ?? null),
                'departemen' => $opts['departemen'] ?? ($emp->Departmen ?? null),
                'sub_departemen' => $opts['sub_departemen'] ?? ($emp->{'Sub Departmen'} ?? null),
                'section' => $opts['section'] ?? ($emp->Section ?? null),
                'level' => $level,
                'level_group' => $opts['level_group'] ?? static::groupOf($level),
                'manager_nik' => $opts['manager_nik'] ?? null,
                'manager_nama' => $opts['manager_nama'] ?? null,
                'associate_manager_nik' => $opts['associate_manager_nik'] ?? null,
                'associate_manager_nama' => $opts['associate_manager_nama'] ?? null,
                'jumlah_bawahan' => $opts['jumlah_bawahan'] ?? null,
                'pendidikan' => $opts['pendidikan'] ?? static::pendidikanFor($nik),
                'tanggal_join' => $emp->{'Tgl Masuk'} ?? null,
                'tanggal_lahir' => $tanggalLahir,
                'usia' => $tanggalLahir ? (int) Carbon::parse($tanggalLahir)->age : null,
                'source' => $opts['source'] ?? null,
                'captured_at' => now(),
            ]);
        } catch (QueryException $e) {
            // Balapan insert oleh proses lain: pakai baris yang sudah ada.
            return HrPmsEmployeeSnapshot::where('periode', $periode)->where('nik', $nik)->first();
        }
    }

    /**
     * Capture banyak karyawan sekaligus untuk satu periode.
     *
     * @param  array<string, array>  $optsByNik  opsi tambahan per NIK (mis. manager/associate manager)
     * @return int jumlah snapshot baru
     */
    public static function captureForPeriod(string $periode, array $niks, array $common = [], array $optsByNik = []): int
    {
        $periode = static::normalizePeriode($periode);
        if ($periode === '') {
            return 0;
        }

        $niks = array_values(array_unique(array_filter(array_map(function ($n) {
            return trim((string) $n);
        }, $niks), function ($n) {
            return $n !== '';
        })));

        if (!$niks) {
            return 0;
        }

        $existing = HrPmsEmployeeSnapshot::where('periode', $periode)
            ->whereIn('nik', $niks)
            ->pluck('nik')
            ->flip();

        $created = 0;
        foreach ($niks as $nik) {
            $opts = array_merge($common, $optsByNik[$nik] ?? []);
            if (isset($existing[$nik])) {
                static::fillDeferred($periode, $nik, $opts);
                continue;
            }
            if (static::capture($periode, $nik, $opts) !== null) {
                $created++;
            }
        }

        return $created;
    }

    /**
     * Pasangan manager/associate manager dari otorisasi IPP periode tertentu (baris terakhir).
     *
     * @return array{manager: ?array, associate_manager: ?array}
     */
    public static function ippOtorisasiPair(?string $periode, ?string $nik): array
    {
        $periode = static::normalizePeriode((string) $periode);
        $nik = trim((string) $nik);
        if ($periode === '' || $nik === '') {
            return ['manager' => null, 'associate_manager' => null];
        }

        $row = DB::table('hr_ipp_otorisasi as o')
            ->join('hr_ipp as i', 'i.id', '=', 'o.ipp_id')
            ->where('i.periode', $periode)
            ->where('o.nik_yang_bersangkutan', $nik)
            ->orderByDesc('o.id')
            ->first(['o.nik_manager', 'o.nama_manager', 'o.nik_associate_manager', 'o.nama_associate_manager']);

        if (!$row) {
            return ['manager' => null, 'associate_manager' => null];
        }

        $mgrNik = trim((string) $row->nik_manager);
        $amNik = trim((string) $row->nik_associate_manager);

        return [
            'manager' => $mgrNik !== '' ? ['nik' => $mgrNik, 'nama' => $row->nama_manager] : null,
            'associate_manager' => $amNik !== '' ? ['nik' => $amNik, 'nama' => $row->nama_associate_manager] : null,
        ];
    }

    /**
     * Pilih pasangan atasan: logic Daftar Peserta PK (PkAtasan) diutamakan,
     * fallback ke otorisasi IPP bila keduanya kosong.
     *
     * @param  array{manager: ?array, associate_manager: ?array}  $pkPair
     * @param  array{manager: ?array, associate_manager: ?array}  $ippPair
     * @return array{manager: ?array, associate_manager: ?array}
     */
    public static function chooseAtasanPair(array $pkPair, array $ippPair): array
    {
        $pkManager = $pkPair['manager'] ?? null;
        $pkAm = $pkPair['associate_manager'] ?? null;

        if ($pkManager || $pkAm) {
            return ['manager' => $pkManager, 'associate_manager' => $pkAm];
        }

        return [
            'manager' => $ippPair['manager'] ?? null,
            'associate_manager' => $ippPair['associate_manager'] ?? null,
        ];
    }

    /**
     * Opsi manager/associate manager siap simpan ke snapshot.
     *
     * @return array{manager_nik: ?string, manager_nama: ?string, associate_manager_nik: ?string, associate_manager_nama: ?string}
     */
    public static function atasanOptsFor(object $emp, ?PkAtasan $resolver = null, ?string $periode = null): array
    {
        $resolver = $resolver ?: new PkAtasan();

        $pkPair = $resolver->resolveAll($emp);
        $ippPair = static::ippOtorisasiPair($periode, $emp->NIK ?? null);
        $pair = static::chooseAtasanPair($pkPair, $ippPair);

        $manager = $pair['manager'] ?? null;
        $am = $pair['associate_manager'] ?? null;

        return [
            'manager_nik' => $manager['nik'] ?? null,
            'manager_nama' => $manager['nama'] ?? null,
            'associate_manager_nik' => $am['nik'] ?? null,
            'associate_manager_nama' => $am['nama'] ?? null,
        ];
    }

    /**
     * Isi field deferred bila masih kosong (tidak menimpa nilai yang sudah ada).
     */
    public static function fillDeferred(string $periode, string $nik, array $data): void
    {
        $periode = static::normalizePeriode($periode);
        $nik = trim($nik);
        if ($periode === '' || $nik === '') {
            return;
        }

        $row = HrPmsEmployeeSnapshot::where('periode', $periode)->where('nik', $nik)->first();
        if (!$row) {
            return;
        }

        $updates = [];
        foreach (self::DEFERRED as $field) {
            $current = $row->{$field};
            if (($current === null || $current === '') && array_key_exists($field, $data)
                && $data[$field] !== null && $data[$field] !== '') {
                $updates[$field] = $data[$field];
            }
        }

        if ($updates) {
            $updates['updated_at'] = now();
            HrPmsEmployeeSnapshot::where('id', $row->id)->update($updates);
        }
    }

    /**
     * Isi field deferred untuk SEMUA periode milik satu NIK (mis. pendidikan).
     *
     * @return int jumlah baris yang ter-update
     */
    public static function fillDeferredForNik(string $nik, array $data): int
    {
        $nik = trim($nik);
        if ($nik === '') {
            return 0;
        }

        $updated = 0;
        foreach (self::DEFERRED as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                continue;
            }
            $updated += HrPmsEmployeeSnapshot::where('nik', $nik)
                ->where(function ($q) use ($field) {
                    $q->whereNull($field)->orWhere($field, '');
                })
                ->update([$field => $data[$field], 'updated_at' => now()]);
        }

        return $updated;
    }

    /**
     * Sinkronkan jumlah_bawahan periode tertentu (menimpa nilai lama).
     *
     * Dipakai saat user mengubah jumlah bawahan langsung di form IPP,
     * supaya form, PDF, dan halaman lain tetap memakai nilai yang sama.
     */
    public static function syncJumlahBawahan(string $periode, string $nik, $value): void
    {
        $periode = static::normalizePeriode($periode);
        $nik = trim($nik);
        if ($periode === '' || $nik === '' || $value === null || $value === '') {
            return;
        }

        HrPmsEmployeeSnapshot::where('periode', $periode)
            ->where('nik', $nik)
            ->update([
                'jumlah_bawahan' => (int) $value,
                'updated_at' => now(),
            ]);
    }

    /**
     * @return array{0: ?int, 1: ?string} [jumlah_bawahan, sumber]
     */
    public static function jumlahBawahanFor(string $periode, string $nik): array
    {
        $periode = static::normalizePeriode($periode);
        $nik = trim($nik);
        if ($periode === '' || $nik === '') {
            return [null, null];
        }

        $oto = DB::table('hr_ipp_otorisasi as o')
            ->join('hr_ipp as i', 'i.id', '=', 'o.ipp_id')
            ->where('i.periode', $periode)
            ->where('o.nik_yang_bersangkutan', $nik)
            ->whereNotNull('o.jumlah_bawahan')
            ->orderByDesc('o.id')
            ->first(['o.jumlah_bawahan']);
        if ($oto && $oto->jumlah_bawahan !== null) {
            return [(int) $oto->jumlah_bawahan, 'ipp'];
        }

        return [null, null];
    }

    public static function pendidikanFor(string $nik): ?string
    {
        $value = DB::table('hr_master_employee_pendidikan')
            ->where('nik', trim($nik))
            ->value('pendidikan');

        return $value !== null && trim((string) $value) !== '' ? (string) $value : null;
    }

    /**
     * Snapshot untuk kombinasi periode + NIK, key: "periode|nik".
     *
     * @return Collection<string, HrPmsEmployeeSnapshot>
     */
    public static function mapFor(array $periodes, array $niks): Collection
    {
        $periodes = array_values(array_unique(array_filter(array_map(function ($p) {
            return static::normalizePeriode((string) $p);
        }, $periodes))));
        $niks = array_values(array_unique(array_filter(array_map(function ($n) {
            return trim((string) $n);
        }, $niks), function ($n) {
            return $n !== '';
        })));

        if (!$periodes || !$niks) {
            return collect();
        }

        return HrPmsEmployeeSnapshot::whereIn('periode', $periodes)
            ->whereIn('nik', $niks)
            ->get()
            ->keyBy(function ($row) {
                return $row->periode . '|' . $row->nik;
            });
    }

    public static function attributesFor(string $periode, string $nik): ?HrPmsEmployeeSnapshot
    {
        $periode = static::normalizePeriode($periode);
        $nik = trim($nik);
        if ($periode === '' || $nik === '') {
            return null;
        }

        return HrPmsEmployeeSnapshot::where('periode', $periode)->where('nik', $nik)->first();
    }

    /**
     * Opsi peserta dari snapshot periode tertentu (tanpa fallback master),
     * dipakai form IPP: dept efektif, posisi, golongan, dan daftar peserta
     * level_group 4-5 & 6-7 (fallback level 4-7 untuk baris lama tanpa level_group;
     * level_group 2-3 dikelola melalui IPP rubrik).
     *
     * @return array{total: int, dept: array<int, string>, positions: array<int, array{v: string, dept: string, level: ?string}>, levels: array<int, string>, peserta: array<int, array{nik: string, nama: string, dept: string, position: string, level: string}>}
     */
    public static function optionSetFor(?string $periode): array
    {
        return static::buildOptionSet(static::normalizePeriode((string) $periode), null);
    }

    public static function optionSetForTeam(?string $periode, string $nik): array
    {
        $nik = trim($nik);
        if ($nik === '') {
            return ['total' => 0, 'dept' => [], 'positions' => [], 'levels' => [], 'peserta' => []];
        }

        return static::buildOptionSet(static::normalizePeriode((string) $periode), $nik);
    }

    private static function buildOptionSet(string $periode, ?string $teamNik): array
    {
        if ($periode === '') {
            return ['total' => 0, 'dept' => [], 'positions' => [], 'levels' => [], 'peserta' => []];
        }

        $query = HrPmsEmployeeSnapshot::where('periode', $periode)
            ->where(function ($q) {
                $q->whereIn('level_group', ['4-5', '6-7'])
                    ->orWhereRaw("COALESCE(level_group, '') = '' AND CAST(level AS UNSIGNED) BETWEEN 4 AND 7");
            });

        if ($teamNik !== null) {
            $query->where(function ($q) use ($teamNik) {
                $q->where('manager_nik', $teamNik)
                    ->orWhere('associate_manager_nik', $teamNik);
            });
        }

        $rows = $query->get(['nik', 'nama', 'jabatan', 'departemen', 'sub_departemen', 'level']);

        $depts = [];
        $positions = [];
        $posLevels = [];
        $levels = [];
        $peserta = [];

        foreach ($rows as $r) {
            $dept = trim((string) $r->departemen);
            $sub = trim((string) $r->sub_departemen);
            if (strtolower($dept) === 'factory') {
                $dept = $sub !== '' ? $sub : $dept;
            }

            if ($dept !== '') {
                $depts[$dept] = true;
            }

            $level = (int) $r->level;
            if ($level > 0) {
                $levels[$level] = true;
            }

            $jabatan = trim((string) $r->jabatan);
            if ($jabatan !== '') {
                $key = $jabatan . '|' . $dept;
                $positions[$key] = ['v' => $jabatan, 'dept' => $dept];
                if ($level > 0) {
                    $posLevels[$key][$level] = ($posLevels[$key][$level] ?? 0) + 1;
                }
            }

            $nik = trim((string) $r->nik);
            if ($nik !== '') {
                $peserta[] = [
                    'nik' => $nik,
                    'nama' => trim((string) $r->nama),
                    'dept' => $dept,
                    'position' => $jabatan,
                    'level' => (string) $level,
                ];
            }
        }

        $deptList = array_keys($depts);
        sort($deptList);

        $levelList = array_keys($levels);
        sort($levelList, SORT_NUMERIC);

        $positionList = [];
        foreach ($positions as $key => $pos) {
            $best = null;
            $counts = $posLevels[$key] ?? [];
            if ($counts) {
                $max = max($counts);
                $candidates = array_keys(array_filter($counts, function ($c) use ($max) {
                    return $c === $max;
                }));
                sort($candidates, SORT_NUMERIC);
                $best = (string) $candidates[0];
            }
            $pos['level'] = $best;
            $positionList[] = $pos;
        }
        usort($positionList, function ($a, $b) {
            return strcmp($a['v'] . '|' . $a['dept'], $b['v'] . '|' . $b['dept']);
        });

        usort($peserta, function ($a, $b) {
            return strcmp($a['nama'] . '|' . $a['nik'], $b['nama'] . '|' . $b['nik']);
        });

        return [
            'total' => $rows->count(),
            'dept' => $deptList,
            'positions' => $positionList,
            'levels' => array_map('strval', $levelList),
            'peserta' => $peserta,
        ];
    }

    /**
     * Opsi filter departemen/bagian dari snapshot (fallback-pelengkap dari master).
     *
     * @return array<int, array{v: string, t: string}>
     */
    public static function deptOptions(?string $periode = null): array
    {
        $q = HrPmsEmployeeSnapshot::query();
        if ($periode !== null && trim($periode) !== '') {
            $q->where('periode', static::normalizePeriode($periode));
        }
        $rows = $q->get(['departemen', 'sub_departemen'])->all();

        $base = fn () => DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereRaw("Level IN ('4', '5', '6', '7')");

        foreach ($base()->whereNotNull('Departmen')->where('Departmen', '<>', '')->distinct()->pluck('Departmen') as $d) {
            $rows[] = (object) ['departemen' => $d, 'sub_departemen' => null];
        }
        foreach ($base()->whereRaw('LOWER(Departmen) = ?', ['factory'])
            ->whereNotNull('Sub Departmen')->where('Sub Departmen', '<>', '')
            ->distinct()->pluck('Sub Departmen') as $s) {
            $rows[] = (object) ['departemen' => 'Factory', 'sub_departemen' => $s];
        }

        return static::collectDeptOptions($rows);
    }

    /**
     * Opsi filter departemen/bagian khusus atasan: hanya dari anak buah
     * langsung (manager_nik) pada snapshot terpilih, tanpa pelengkap master
     * agar tidak membocorkan seluruh departemen perusahaan.
     *
     * @return array<int, array{v: string, t: string}>
     */
    public static function deptOptionsForManager(string $managerNik, ?string $periode = null): array
    {
        $managerNik = trim($managerNik);
        if ($managerNik === '') {
            return [];
        }

        $q = HrPmsEmployeeSnapshot::where('manager_nik', $managerNik);
        if ($periode !== null && trim($periode) !== '') {
            $q->where('periode', static::normalizePeriode($periode));
        }

        return static::collectDeptOptions($q->get(['departemen', 'sub_departemen'])->all(), true);
    }

    /**
     * @param  iterable<object>  $rows
     * @param  bool  $skipFactoryDept  buang opsi dept "Factory" (filter dept
     *         Factory tidak pernah cocok; anggotanya disaring lewat sub).
     * @return array<int, array{v: string, t: string}>
     */
    private static function collectDeptOptions(iterable $rows, bool $skipFactoryDept = false): array
    {
        $depts = [];
        $subs = [];

        foreach ($rows as $r) {
            $dept = trim((string) ($r->departemen ?? ''));
            $sub = trim((string) ($r->sub_departemen ?? ''));
            if ($dept !== '' && !($skipFactoryDept && strtolower($dept) === 'factory')) {
                $depts[$dept] = true;
            }
            if ($sub !== '' && strtolower($dept) === 'factory') {
                $subs[$sub] = true;
            }
        }

        $deptList = array_keys($depts);
        $subList = array_keys($subs);
        sort($deptList);
        sort($subList);

        $out = [];
        foreach ($deptList as $d) {
            $out[] = ['v' => $d, 't' => 'dept'];
        }
        foreach ($subList as $s) {
            $out[] = ['v' => $s, 't' => 'sub'];
        }

        return $out;
    }

    /**
     * Label tampilan Departemen & Bagian (paritas export /hr/ipp-approval dan
     * /hr/pk-assessment): Factory memakai sub-departemen sebagai DEPT dan
     * Section sebagai BAGIAN; sebaliknya (non-Factory) Sub Departemen menjadi
     * BAGIAN dengan Section sebagai cadangan.
     *
     * @return array{dept: string, bagian: string}
     */
    public static function displayDeptBagian(string $departemen, string $subDepartemen, string $section): array
    {
        $dept = trim($departemen);
        $sub = trim($subDepartemen);
        $sec = trim($section);

        if (strtolower($dept) === 'factory') {
            return [
                'dept' => $sub !== '' ? $sub : $dept,
                'bagian' => $sec !== '' ? $sec : $sub,
            ];
        }

        return [
            'dept' => $dept,
            'bagian' => $sub !== '' ? $sub : $sec,
        ];
    }
}
