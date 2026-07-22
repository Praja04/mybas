<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\HrMasterEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HrDashboardController extends Controller
{
    /**
     * Hardcoded NIK yang diizinkan untuk mengakses HR Dashboard
     * (beserta filter Departmen & Sub Departmen di dalamnya).
     * Untuk sementara hanya NIK di list ini yang boleh masuk halaman.
     * TODO: pindahkan ke tabel permission / config bila sudah final.
     */
    private const AUTHORIZED_NIKS = [
        '010319-28921',
        '020517-24948',
        '051213-14351',
        '060223-43478',
        '0483', //pak yongki 105000483
        '150421-35816',
        '261212-12105',
        '305000756',
    ];

    /**
     * Codename permission yang memberikan full access (setara AUTHORIZED_NIKS)
     * kepada user yang memilikinya, baik via group maupun direct (auth_user_permission).
     */
    private const ALL_OTORISASI_CODENAME = 'hrdashboard_all_otorisasi';

    /**
     * Mapping query param `?type_karyawan=...` ke daftar Tipe Karyawan yang
     * diizinkan untuk mode tersebut. Berlaku untuk semua user (AUTHORIZED_NIKS
     * maupun restricted) — hanya me-restrict filter Tipe Karyawan saja.
     *
     *   - mitra_kerja → KMJ & Fortuna (data mitra kerja)
     *   - BAS         → Staff & Non Staff (data internal)
     *   - (lainnya)   → tidak ada restriction, tampilkan semua
     */
    private const TIPE_KARYAWAN_MODES = [
        'mitra_kerja' => ['KMJ', 'Fortuna'],
        'BAS'         => ['Staff', 'Non Staff'],
    ];

    private function getTipeKaryawanMode(?string $mode): ?array
    {
        if ($mode === null || $mode === '') {
            return null;
        }
        return self::TIPE_KARYAWAN_MODES[$mode] ?? null;
    }

    /**
     * Cek apakah user mendapat full access (bypass filter dept/sub-dept):
     *  1) Hardcoded NIK di AUTHORIZED_NIKS (backward compat)
     *  2) User punya permission codename 'hrdashboard_all_otorisasi'
     *     (via group atau direct)
     */
    private function isAuthorizedNiks(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $nik      = (string) ($user->nik ?? '');
        $username = (string) ($user->username ?? '');

        // 1) Hardcoded NIKs
        if (in_array($nik, self::AUTHORIZED_NIKS, true)
            || in_array($username, self::AUTHORIZED_NIKS, true)) {
            return true;
        }

        // 2) Permission 'hrdashboard_all_otorisasi' (group + direct)
        $codenames = $user->getAllPermissionCodenames() ?? [];
        return in_array(self::ALL_OTORISASI_CODENAME, $codenames, true);
    }

    /**
     * Cek apakah user punya akses HR Dashboard.
     * 1) Hardcoded AUTHORIZED_NIKS → full access (backward compat)
     * 2) Permission 'hrdashboard_all_otorisasi' → full access
     * 3) User dengan permission ber-prefix "hrdashboard" → akses (restricted)
     */
    private function hasDashboardAccess(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        if ($this->isAuthorizedNiks()) {
            return true;
        }
        $user = Auth::user();
        return !empty($user->getPrefixPermission('hrdashboard'));
    }

    /**
     * Cek apakah user punya akses ke Top 10 Karyawan Lembur.
     * Hanya user dengan permission spesifik "hrdashboard_top_lembur"
     * (atau AUTHORIZED_NIKS / permission top-lembur spesifik).
     * Sub-dept / dept permission saja TIDAK cukup.
     */
    private function hasTopLemburAccess(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        if ($this->isAuthorizedNiks()) {
            return true;
        }
        $user = Auth::user();
        $codenames = $user->getAllPermissionCodenames() ?? [];
        return in_array('hrdashboard_top_lembur', $codenames, true);
    }

    /**
     * Kembalikan akses yg diizinkan untuk user yang login.
     * Return structure:
     *   - null        = full access (AUTHORIZED_NIKS atau superuser)
     *   - ['depts' => [], 'sub_depts' => []] = no access
     *   - ['depts' => ['Factory', ...], 'sub_depts' => ['PN1', ...]] = restricted
     *
     * Field 'depts': daftar departemen dari permission departemen-level — user
     *   boleh lihat SEMUA baris di departemen tsb, termasuk yang Sub Departmen-nya
     *   NULL/kosong (yang tidak punya nilai sub-departemen).
     * Field 'sub_depts': daftar sub-departemen eksplisit dari permission
     *   sub-departemen-level — user hanya boleh lihat baris dengan sub-dept tsb.
     */
    private function getAllowedAccessForUser(): ?array
    {
        if (!Auth::check()) {
            return ['depts' => [], 'sub_depts' => []];
        }
        // AUTHORIZED_NIKS = full access
        if ($this->isAuthorizedNiks()) {
            return null;
        }

        $user = Auth::user();
        $userPermNames = $user->getPrefixPermission('hrdashboard');
        if (empty($userPermNames)) {
            return ['depts' => [], 'sub_depts' => []];
        }

        // Mapping khusus untuk handle nama permission yang terpotong saat disimpan
        // (kolom auth_permission.name kepotong max 50 char). Key = nama permission
        // eksisting di DB (terpotong), Value = nama Sub Departmen lengkap yang
        // ada di hr_master_employee. Match exact (case-sensitive) sesuai data.
        $truncatedNameMap = [
            'Engineering Project Mechanical Electrical Automati'
                => 'Engineering Project Mechanical Electrical Automation',
        ];
        $userPermNames = array_map(
            fn ($n) => $truncatedNameMap[$n] ?? $n,
            $userPermNames
        );

        $allDepts    = $this->getCachedDistinct('Departmen');
        $allSubDepts = $this->getCachedDistinct('Sub Departmen');

        // Pisahkan: permission yg match dengan nama departemen vs sub-departemen.
        // PENTING: kalau sebuah nama match KEDUA (dept & sub-dept), perlakukan
        // sebagai dept-level only — agar dept-level permission tidak bocor
        // ke sub-dept filter (yang akan exclude baris dengan Sub Dept kosong).
        $userDeptPerms    = array_values(array_intersect($userPermNames, $allDepts));
        $userSubDeptPerms = array_values(array_diff(
            array_intersect($userPermNames, $allSubDepts),
            $userDeptPerms
        ));

        return [
            'depts'     => $userDeptPerms,
            'sub_depts' => $userSubDeptPerms,
        ];
    }

    /**
     * Cache distinct values dari kolom tertentu di hr_master_employee (1 jam).
     * Mencegah N+1 query pada dropdown population.
     */
    private function getCachedDistinct(string $column): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'hr.distinct.' . $column,
            3600,
            function () use ($column) {
                return HrMasterEmployee::distinct()->orderBy($column)
                    ->pluck($column)->filter()->values()->toArray();
            }
        );
    }

    public function index(Request $request)
    {
        if (!$this->hasDashboardAccess()) {
            abort(403, 'Anda tidak memiliki akses ke HR Dashboard.');
        }

        // Departemen & sub-departemen yang diizinkan untuk user ini
        $access = $this->getAllowedAccessForUser();

        // null = full access (AUTHORIZED_NIKS)
        if ($access === null) {
            $departments    = $this->getCachedDistinct('Departmen');
            $subDepartments = $this->getCachedDistinct('Sub Departmen');
        } else {
            $allowedDepts    = $access['depts'];
            $allowedSubDepts = $access['sub_depts'];

            // Sub-departemen eksplisit (dari permission sub-dept-level)
            $subDeptList = $allowedSubDepts;
            // Plus semua sub-departemen di departemen2 yg punya dept-level permission
            if (!empty($allowedDepts)) {
                $subsInAllowedDepts = HrMasterEmployee::whereIn('Departmen', $allowedDepts)
                    ->distinct()->pluck('Sub Departmen')->filter()->values()->toArray();
                $subDeptList = array_values(array_unique(array_merge($subDeptList, $subsInAllowedDepts)));
            }
            $subDepartments = $subDeptList;

            // Departemen dropdown: dept-level perms + depts yg punya sub-dept eksplisit
            $deptList = $allowedDepts;
            if (!empty($allowedSubDepts)) {
                $deptsForSubDepts = HrMasterEmployee::whereIn('Sub Departmen', $allowedSubDepts)
                    ->distinct()->pluck('Departmen')->toArray();
                $deptList = array_values(array_unique(array_merge($deptList, $deptsForSubDepts)));
            }
            $departments = $deptList;
        }

        $allTypes         = $this->getCachedDistinct('Tipe Karyawan');
        $typeKaryawanMode = $request->get('type_karyawan');
        $modeAllowed      = $this->getTipeKaryawanMode($typeKaryawanMode);
        if ($modeAllowed !== null) {
            $types = array_values(array_intersect($allTypes, $modeAllowed));
        } else {
            $types = $allTypes;
        }

        return view('hr.dashboard.index', [
            'departments'         => $departments,
            'subDepartments'      => $subDepartments,
            'types'               => $types,
            'typeKaryawanMode'    => $typeKaryawanMode,
            'canFilterDepartmen'  => true,
            'canViewTopLembur'    => $this->hasTopLemburAccess(),
        ]);
    }

    public function data(Request $request)
    {
        $perPage = max(1, min((int) $request->get('per_page', 25), 200));
        $page    = max(1, (int) $request->get('page', 1));

        $request = $this->sanitizeFilterRequest($request);

        $baseQuery = $this->buildFilteredQuery($request);

        $total    = (clone $baseQuery)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));

        // Total Karyawan Aktif AS OF Rentang Data
        $totalActive = (clone $baseQuery);
        $this->applyAktifRentangFilter($totalActive, $request);
        $totalActive = $totalActive->count();

        // Karyawan Tetap (Work Status = 'Karyawan Tetap') AS OF Rentang Data
        $karyawanTetap = (clone $baseQuery);
        $this->applyAktifRentangFilter($karyawanTetap, $request);
        $karyawanTetap = $karyawanTetap->where('Work Status', 'Karyawan Tetap')->count();

        // Karyawan Kontrak & Probation (Work Status != 'Karyawan Tetap') AS OF Rentang Data
        $karyawanKontrak = (clone $baseQuery);
        $this->applyAktifRentangFilter($karyawanKontrak, $request);
        $karyawanKontrak = $karyawanKontrak->where('Work Status', '!=', 'Karyawan Tetap')->count();

        // Staff (Tipe Karyawan = 'Staff') AS OF Rentang Data
        $karyawanStaff = (clone $baseQuery);
        $this->applyAktifRentangFilter($karyawanStaff, $request);
        $karyawanStaff = $karyawanStaff->where('Tipe Karyawan', 'Staff')->count();

        // Non Staff (Tipe Karyawan = 'Non Staff') AS OF Rentang Data
        $karyawanNonStaff = (clone $baseQuery);
        $this->applyAktifRentangFilter($karyawanNonStaff, $request);
        $karyawanNonStaff = $karyawanNonStaff->where('Tipe Karyawan', 'Non Staff')->count();

        // === Tipe Karyawan Distribution (untuk chart Employee Type) ===
        // Axis mengikuti ?type_karyawan= mode:
        //   - mitra_kerja → ['KMJ', 'Fortuna']
        //   - BAS         → ['Staff', 'Non Staff']
        //   - (lainnya)   → ['Staff', 'Non Staff']  (default)
        // Dihitung AS OF Rentang Data dengan 1 query aggregate.
        $tipeAxis = $this->getTipeKaryawanMode($request->get('type_karyawan'))
            ?? ['Staff', 'Non Staff'];

        $tipeCountQuery = (clone $baseQuery);
        $this->applyAktifRentangFilter($tipeCountQuery, $request);
        $tipeCountRows = $tipeCountQuery
            ->select('Tipe Karyawan', DB::raw('count(*) as total'))
            ->whereIn('Tipe Karyawan', $tipeAxis)
            ->groupBy('Tipe Karyawan')
            ->pluck('total', 'Tipe Karyawan')
            ->toArray();

        $tipeDistribution = [
            'labels' => $tipeAxis,
            'data'   => array_map(
                fn ($t) => (int) ($tipeCountRows[$t] ?? 0),
                $tipeAxis
            ),
        ];

        // Gender breakdown AS OF Rentang Data
        $genderLaki = (clone $baseQuery);
        $this->applyAktifRentangFilter($genderLaki, $request);
        $genderLaki = $genderLaki->where('Jenis Kelamin', 'L')->count();

        $genderPerempuan = (clone $baseQuery);
        $this->applyAktifRentangFilter($genderPerempuan, $request);
        $genderPerempuan = $genderPerempuan->where('Jenis Kelamin', 'P')->count();

        $genderTotal = $genderLaki + $genderPerempuan;
        $genderLakiPct = $genderTotal > 0 ? round(($genderLaki / $genderTotal) * 100, 1) : 0;
        $genderPerempuanPct = $genderTotal > 0 ? round(($genderPerempuan / $genderTotal) * 100, 1) : 0;

        $rows = (clone $baseQuery)
            ->leftJoin('users', 'users.username', '=', 'hr_master_employee.send_by_username')
            ->select('hr_master_employee.*', 'users.name as updated_by_name')
            ->orderBy('hr_master_employee.NIK')
            ->forPage($page, $perPage)
            ->get();

        // By Department
        $byDepartment = (clone $baseQuery)
            ->select('Departmen', DB::raw('count(*) as total'))
            ->groupBy('Departmen')
            ->orderByDesc('total')
            ->get();

        // By Tipe Karyawan
        $byType = (clone $baseQuery)
            ->select('Tipe Karyawan', DB::raw('count(*) as total'))
            ->groupBy('Tipe Karyawan')
            ->orderByDesc('total')
            ->get();

        // By Aktif status
        $byStatus = (clone $baseQuery)
            ->select('Aktif', DB::raw('count(*) as total'))
            ->groupBy('Aktif')
            ->get();

        // New joiners
        $newJoiners = $this->buildNewJoinersQuery($request)->count();

        // Leavers
        $leavers = $this->buildLeaversQuery($request)->count();

        // Employee In: group by year(Tgl Masuk) & Tipe Karyawan
        // Filter by Tgl Masuk range (BUKAN Valid From) + departemen/sub/tipe
        // Pivot key per tipe mengikuti $tipeAxis (KMJ/Fortuna utk mitra_kerja,
        // Staff/Non Staff utk BAS / default). Filter tipe sudah diaplikasikan
        // oleh applyTipeKaryawanMode() di sanitizeFilterRequest().
        $empInQuery = $this->buildEmployeeInQuery($request);
        $empInRaw = $empInQuery
            ->whereNotNull('Tgl Masuk')
            ->whereIn('Tipe Karyawan', $tipeAxis)
            ->select(
                DB::raw("YEAR(`Tgl Masuk`) as year"),
                'Tipe Karyawan',
                DB::raw('count(*) as total')
            )
            ->groupBy(DB::raw("YEAR(`Tgl Masuk`)"), 'Tipe Karyawan')
            ->orderBy('year')
            ->get();

        $years = $empInRaw->pluck('year')->unique()->sort()->values();
        $empIn = [
            'years' => $years->map(fn ($y) => (int) $y)->toArray(),
        ];
        foreach ($tipeAxis as $t) {
            $empIn[$t] = [];
            foreach ($years as $y) {
                $empIn[$t][] = (int) $empInRaw->where('year', $y)
                    ->where('Tipe Karyawan', $t)->sum('total');
            }
        }

        // Employee Out: group by year(Valid From) & Tipe Karyawan
        // Filter: Aktif = N, Tgl Masuk range, departemen/sub/tipe (BUKAN Valid From range)
        $empOutRaw = $this->buildEmployeeOutQuery($request)
            ->whereNotNull('Valid From')
            ->whereIn('Tipe Karyawan', $tipeAxis)
            ->select(
                DB::raw("YEAR(`Valid From`) as year"),
                'Tipe Karyawan',
                DB::raw('count(*) as total')
            )
            ->groupBy(DB::raw("YEAR(`Valid From`)"), 'Tipe Karyawan')
            ->orderBy('year')
            ->get();

        $outYears = $empOutRaw->pluck('year')->unique()->sort()->values();
        $empOut = [
            'years' => $outYears->map(fn ($y) => (int) $y)->toArray(),
        ];
        foreach ($tipeAxis as $t) {
            $empOut[$t] = [];
            foreach ($outYears as $y) {
                $empOut[$t][] = (int) $empOutRaw->where('year', $y)
                    ->where('Tipe Karyawan', $t)->sum('total');
            }
        }

        // Distribusi Usia: count karyawan by age category AS OF Rentang Data.
        // Umur dihitung AS OF snapshot date (rentang_data_to), bukan hari ini.
        $distribusiUsia = ['>55' => 0, '51-55' => 0, '41-50' => 0, '31-40' => 0, '18-30' => 0, '<18' => 0];

        $usiaQuery = (clone $baseQuery)->whereNotNull('Tgl Lahir');
        $this->applyAktifRentangFilter($usiaQuery, $request);
        $birthdayRows = $usiaQuery->get(['Tgl Lahir', 'Aktif']);

        $referenceDate = $request->filled('rentang_data_to')
            ? \Carbon\Carbon::parse($request->get('rentang_data_to'))
            : now();
        foreach ($birthdayRows as $br) {
            $lahir = \Carbon\Carbon::parse($br->{'Tgl Lahir'});
            $age   = $lahir->diffInYears($referenceDate);
            if      ($age > 55) $distribusiUsia['>55']++;
            elseif ($age >= 51) $distribusiUsia['51-55']++;
            elseif ($age >= 41) $distribusiUsia['41-50']++;
            elseif ($age >= 31) $distribusiUsia['31-40']++;
            elseif ($age >= 18) $distribusiUsia['18-30']++;
            else                $distribusiUsia['<18']++;
        }

        // Monthly Total HeadCount trend (24 bulan, snapshot per akhir bulan)
        $headcountTrend = $this->buildHeadcountTrend($request);

        return response()->json([
            'data'             => $rows,
            'total'            => $total,
            'total_active'     => $totalActive,
            'karyawan_tetap'   => $karyawanTetap,
            'karyawan_kontrak' => $karyawanKontrak,
            'karyawan_staff'   => $karyawanStaff,
            'karyawan_non_staff' => $karyawanNonStaff,
            'tipe_distribution' => $tipeDistribution,
            'gender_laki'      => $genderLaki,
            'gender_perempuan' => $genderPerempuan,
            'gender_total'     => $genderTotal,
            'gender_laki_pct'      => $genderLakiPct,
            'gender_perempuan_pct' => $genderPerempuanPct,
            'page'             => $page,
            'per_page'         => $perPage,
            'last_page'        => $lastPage,
            'by_department'    => $byDepartment,
            'by_type'          => $byType,
            'by_status'        => $byStatus,
            'new_joiners'      => $newJoiners,
            'leavers'          => $leavers,
            'emp_in'           => $empIn,
            'emp_out'          => $empOut,
            'distribusi_usia'  => $distribusiUsia,
            'headcount_trend'  => $headcountTrend,
        ]);
    }

    public function export(Request $request)
    {
        $request = $this->sanitizeFilterRequest($request);
        $rows = $this->buildFilteredQuery($request)->get();

        $endDate = $request->get('rentang_data_to'); // end date snapshot, atau null jika tidak diisi

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="hr_dashboard_export.csv"',
        ];

        $callback = function () use ($rows, $endDate) {
            $fh = fopen('php://output', 'w');
            // BOM for UTF-8
            fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($fh, [
                'NIK', 'Nama', 'Tgl Lahir', 'Tgl Masuk', 'Tgl Keluar (Valid From)',
                'Departmen', 'Sub Departmen', 'Section', 'Tipe Karyawan', 'Jabatan',
                'Jenis Kelamin', 'Work Status', 'Status Nikah', 'Aktif',
                'Aktif (Rentang Data)',
            ]);

            foreach ($rows as $r) {
                // Tgl Keluar (Valid From): sama dengan logika di view —
                // kosongkan bila Aktif = Y, tampilkan bila Aktif = N.
                $tglKeluar = (strtoupper((string) $r->Aktif) === 'N')
                    ? ($r->{'Valid From'} ?? '')
                    : '';
                fputcsv($fh, [
                    $r->NIK, $r->Nama, $r->{'Tgl Lahir'}, $r->{'Tgl Masuk'},
                    $tglKeluar, $r->Departmen, $r->{'Sub Departmen'},
                    $r->Section, $r->{'Tipe Karyawan'}, $r->Jabatan,
                    $r->{'Jenis Kelamin'}, $r->{'Work Status'}, $r->{'Status Nikah'}, $r->Aktif,
                    $this->calcAktifRentangData($r, $endDate),
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Status aktif AS OF rentang data snapshot.
     * Logika sama dengan calcAktifRentangData() di view (client-side).
     */
    private function calcAktifRentangData($r, ?string $endDate): string
    {
        if (empty($endDate)) return '-';
        if (strtoupper((string) $r->Aktif) === 'Y') return 'Y';
        $vf = $r->{'Valid From'};
        if (empty($vf)) return 'Y';
        return strtotime($vf) > strtotime($endDate) ? 'Y' : 'N';
    }

    /**
     * Restrict filter departmen/sub_departmen ke yg diizinkan user.
     * - AUTHORIZED_NIKS: full access (return as-is)
     * - User dengan permission "hrdashboard_*": filter ke sub-departemen yg
     *   diizinkan (expanded dari dept-level permission bila ada)
     * - User tanpa akses: strip semua filter departemen
     * Plus: apply ?type_karyawan= mode restriction (jika ada) ke filter
     *       Tipe Karyawan. Berlaku untuk semua user, SEBELUM early-return
     *       AUTHORIZED_NIKS.
     * Return Request yg sudah aman dipakai oleh build*Query().
     */
    private function sanitizeFilterRequest(Request $request): Request
    {
        // Apply ?type_karyawan= mode restriction (untuk semua user)
        $request = $this->applyTipeKaryawanMode($request);

        // Full-access users: return as-is (mode sudah diaplikasikan di atas)
        if ($this->isAuthorizedNiks()) {
            return $request;
        }

        $access = $this->getAllowedAccessForUser();
        $allowedDepts    = $access['depts'] ?? [];
        $allowedSubDepts = $access['sub_depts'] ?? [];

        // User tanpa permission = tidak boleh pilih apa-apa
        if (empty($allowedDepts) && empty($allowedSubDepts)) {
            $request->query->remove('departmen');
            $request->query->remove('sub_departmen');
            $request->request->remove('departmen');
            $request->request->remove('sub_departmen');
            return $request;
        }

        // Expand: kalau user punya dept-level permission, tambahkan semua
        // sub-departemen di dept2 tsb ke allowedSubDepts — sama dengan logika
        // dropdown di index(). Tanpa expand ini, user dept-only akan punya
        // allowedSubDepts = [] → filter Sub Departmen IN () → 0 rows.
        if (!empty($allowedDepts)) {
            $subsInAllowedDepts = HrMasterEmployee::whereIn('Departmen', $allowedDepts)
                ->distinct()->pluck('Sub Departmen')->filter()->values()->toArray();
            $allowedSubDepts = array_values(array_unique(
                array_merge($allowedSubDepts, $subsInAllowedDepts)
            ));
        }

        // Restrict sub_departmen ke yg diizinkan user
        $requestedSubs = $request->input('sub_departmen', []);
        if (is_string($requestedSubs)) {
            $requestedSubs = [$requestedSubs];
        }
        $validSubs = array_values(array_intersect($requestedSubs, $allowedSubDepts));
        if (!empty($validSubs)) {
            $request->merge(['sub_departmen' => $validSubs]);
        } else {
            // User tidak pilih sub_departmen → set default ke semua yg diizinkan
            $request->merge(['sub_departmen' => $allowedSubDepts]);
        }

        // Restrict departmen: union dept-level perms dengan depts yg punya
        // sub-departemen diizinkan. user dengan dept-only permission TETAP
        // boleh pilih departemen tsb meskipun sub_departmen ada yg kosong.
        $deptsFromSubs = !empty($allowedSubDepts)
            ? HrMasterEmployee::whereIn('Sub Departmen', $allowedSubDepts)
                ->distinct()->pluck('Departmen')->toArray()
            : [];
        $allowedDeptsFinal = array_values(array_unique(
            array_merge($allowedDepts, $deptsFromSubs)
        ));
        $requestedDepts = $request->input('departmen', []);
        if (is_string($requestedDepts)) {
            $requestedDepts = [$requestedDepts];
        }
        $validDepts = array_values(array_intersect($requestedDepts, $allowedDeptsFinal));
        if (!empty($validDepts)) {
            $request->merge(['departmen' => $validDepts]);
        } else {
            $request->merge(['departmen' => $allowedDeptsFinal]);
        }

        return $request;
    }

    /**
     * Apply restriction `?type_karyawan=mitra_kerja|BAS` ke filter
     * `tipe_karyawan[]` di request. Jika user di mode tertentu:
     *   - hasil intersect dengan allowed values; jika kosong → default
     *     ke SEMUA allowed values, agar user tetap melihat data untuk
     *     mode-nya meskipun checkbox dikosongkan.
     * Jika tidak ada mode / mode tidak dikenal → return as-is.
     */
    private function applyTipeKaryawanMode(Request $request): Request
    {
        $mode    = $request->get('type_karyawan');
        $allowed = $this->getTipeKaryawanMode($mode);
        if ($allowed === null) {
            return $request;
        }

        $types       = $this->getArrayFilter($request, 'tipe_karyawan');
        $intersected = array_values(array_intersect($types, $allowed));

        if (empty($intersected)) {
            $intersected = $allowed;
        }

        $request->merge(['tipe_karyawan' => $intersected]);
        return $request;
    }

    /**
     * Apply filter akses user ke query builder.
     * Pakai OR: WHERE (Departmen IN dept_perms OR Sub Departmen IN sub_dept_perms).
     * Dept-level permission otomatis meng-include baris dengan Sub Departmen kosong
     * (NULL/'') di departemen tsb — karena filter hanya cek Departmen.
     * Sub-dept-level permission hanya match baris dengan Sub Departmen tsb.
     */
    private function applyUserAccessFilter($query, Request $request)
    {
        if ($this->isAuthorizedNiks()) {
            return $query;
        }
        $access = $this->getAllowedAccessForUser();
        $allowedDepts    = $access['depts'] ?? [];
        $allowedSubDepts = $access['sub_depts'] ?? [];

        if (empty($allowedDepts) && empty($allowedSubDepts)) {
            $query->whereRaw('1 = 0');
            return $query;
        }

        $query->where(function ($q) use ($allowedDepts, $allowedSubDepts) {
            if (!empty($allowedDepts)) {
                $q->orWhereIn('Departmen', $allowedDepts);
            }
            if (!empty($allowedSubDepts)) {
                $q->orWhereIn('Sub Departmen', $allowedSubDepts);
            }
        });

        return $query;
    }

    /**
     * Apply filter "Aktif (Rentang Data) = Y" ke query builder.
     * - Jika rentang_data_to diisi: pakai logika snapshot (Aktif=Y ATAU
     *   (Aktif=N AND Valid From > end_date)) — sama dengan kolom di tabel.
     * - Jika tidak: fallback ke Aktif = 'Y' mentah.
     */
    private function applyAktifRentangFilter($query, Request $request)
    {
        if ($request->filled('rentang_data_to')) {
            $endDate = $request->get('rentang_data_to');
            $query->where(function ($q) use ($endDate) {
                $q->where('Aktif', 'Y')
                  ->orWhere(function ($sub) use ($endDate) {
                      $sub->where('Aktif', 'N')
                          ->where(function ($s) use ($endDate) {
                              $s->whereNull('Valid From')
                                ->orWhere('Valid From', '>', $endDate);
                          });
                  });
            });
        } else {
            $query->where('Aktif', 'Y');
        }
        return $query;
    }

    private function buildFilteredQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = HrMasterEmployee::query();

        // Apply user access filter (dept-level + sub-dept-level permission)
        // HARUS dijalankan pertama, sebelum filter lain, agar tidak bocor.
        $this->applyUserAccessFilter($query, $request);

        // Rentang Data = point-in-time headcount snapshot.
        // Tampilkan karyawan yang Tgl Masuk-nya <= rentang_data_to
        // (karyawan yang pernah/sedang aktif s/d tanggal snapshot).
        // Filter ini untuk Aktif = 'Y' maupun Aktif = 'N', jadi leavers
        // yang sebelumnya sempat aktif di periode snapshot tetap muncul.
        if ($request->filled('rentang_data_to')) {
            $endDate = $request->get('rentang_data_to');
            $query->where(function ($q) use ($endDate) {
                $q->where(function ($sub) use ($endDate) {
                    $sub->where('Aktif', 'Y')
                        ->where('Tgl Masuk', '<=', $endDate);
                })->orWhere(function ($sub) use ($endDate) {
                    $sub->where('Aktif', 'N')
                        ->where('Tgl Masuk', '<=', $endDate);
                });
            });
        }

        // Tgl Masuk range (untuk chart joiners/in — tambahan opsional).
        // Tgl Masuk filter selalu dipakai kalau user isi, independent dari
        // rentang_data_to. Tgl Masuk <= end_date di OR group Rentang Data
        // adalah constraint INTERNAL (snapshot), sedangkan filter ini eksplisit
        // dari user.
        if ($request->filled('tgl_masuk_from')) {
            $query->where('Tgl Masuk', '>=', $request->get('tgl_masuk_from'));
        }
        if ($request->filled('tgl_masuk_to')) {
            $query->where('Tgl Masuk', '<=', $request->get('tgl_masuk_to'));
        }

        // Filter Tgl Keluar (Valid From): hanya untuk karyawan non-aktif (Aktif = N)
        if ($request->filled('tgl_keluar_from') || $request->filled('tgl_keluar_to')) {
            $query->where('Aktif', 'N');
            if ($request->filled('tgl_keluar_from')) {
                $query->where('Valid From', '>=', $request->get('tgl_keluar_from'));
            }
            if ($request->filled('tgl_keluar_to')) {
                $query->where('Valid From', '<=', $request->get('tgl_keluar_to'));
            }
        }

        // Filter Departmen
        $depts = $this->getArrayFilter($request, 'departmen');
        if (!empty($depts)) {
            $query->whereIn('Departmen', $depts);
        }

        // Filter Sub Departmen
        $subs = $this->getArrayFilter($request, 'sub_departmen');
        if (!empty($subs)) {
            $query->whereIn('Sub Departmen', $subs);
        }

        // Filter Tipe Karyawan
        $types = $this->getArrayFilter($request, 'tipe_karyawan');
        if (!empty($types)) {
            $query->whereIn('Tipe Karyawan', $types);
        }

        return $query->orderBy('NIK');
    }

    private function buildNewJoinersQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = HrMasterEmployee::query();

        if ($request->filled('tgl_masuk_from')) {
            $query->where('Tgl Masuk', '>=', $request->get('tgl_masuk_from'));
        }
        if ($request->filled('tgl_masuk_to')) {
            $query->where('Tgl Masuk', '<=', $request->get('tgl_masuk_to'));
        }

        $depts = $this->getArrayFilter($request, 'departmen');
        if (!empty($depts)) {
            $query->whereIn('Departmen', $depts);
        }
        $subs = $this->getArrayFilter($request, 'sub_departmen');
        if (!empty($subs)) {
            $query->whereIn('Sub Departmen', $subs);
        }
        $types = $this->getArrayFilter($request, 'tipe_karyawan');
        if (!empty($types)) {
            $query->whereIn('Tipe Karyawan', $types);
        }

        return $query;
    }

    private function buildLeaversQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = HrMasterEmployee::query();

        // Filter Tgl Keluar (Valid From)
        if ($request->filled('tgl_keluar_from')) {
            $query->where('Valid From', '>=', $request->get('tgl_keluar_from'));
        }
        if ($request->filled('tgl_keluar_to')) {
            $query->where('Valid From', '<=', $request->get('tgl_keluar_to'));
        }

        $depts = $this->getArrayFilter($request, 'departmen');
        if (!empty($depts)) {
            $query->whereIn('Departmen', $depts);
        }
        $subs = $this->getArrayFilter($request, 'sub_departmen');
        if (!empty($subs)) {
            $query->whereIn('Sub Departmen', $subs);
        }
        $types = $this->getArrayFilter($request, 'tipe_karyawan');
        if (!empty($types)) {
            $query->whereIn('Tipe Karyawan', $types);
        }

        return $query;
    }

    private function buildEmployeeInQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = HrMasterEmployee::query();

        // Employee In = "kumulatif yang pernah masuk": hitung SEMUA karyawan
        // per tahun Tgl Masuk, terlepas dari status Aktif saat ini (Aktif='Y'
        // maupun yang sudah keluar Aktif='N'). Leavers TETAP dihitung di sini
        // sebagai joiner di tahun dia masuk. Yang membedakan dengan chart
        // Employee Out: di sini sumber tahun = Tgl Masuk (tanggal masuk), di
        // Employee Out sumber tahun = Valid From (tanggal keluar).
        //
        // Tidak ada filter Aktif (Rentang Data) di sini.
        //
        // Filter range Tgl Masuk:
        //   - Batas bawah: HANYA tgl_masuk_from eksplisit dari user (jika diisi).
        //     rentang_data_from TIDAK dipakai sebagai batas bawah, supaya chart
        //     tetap menampilkan joiner dari tahun terawal. Contoh: rentang data
        //     2025-12-01 s/d 2025-12-31 → chart tetap tampil dari tahun terawal
        //     hingga 2025, bukan cuma Desember 2025.
        //   - Batas atas: tgl_masuk_to eksplisit, atau fallback rentang_data_to
        //     (snapshot). Ini membatasi agar joiner setelah snapshot tidak
        //     dihitung.
        if ($request->filled('tgl_masuk_from')) {
            $query->where('Tgl Masuk', '>=', $request->get('tgl_masuk_from'));
        }
        if ($request->filled('tgl_masuk_to')) {
            $query->where('Tgl Masuk', '<=', $request->get('tgl_masuk_to'));
        } elseif ($request->filled('rentang_data_to')) {
            $query->where('Tgl Masuk', '<=', $request->get('rentang_data_to'));
        }

        $depts = $this->getArrayFilter($request, 'departmen');
        if (!empty($depts)) {
            $query->whereIn('Departmen', $depts);
        }
        $subs = $this->getArrayFilter($request, 'sub_departmen');
        if (!empty($subs)) {
            $query->whereIn('Sub Departmen', $subs);
        }
        $types = $this->getArrayFilter($request, 'tipe_karyawan');
        if (!empty($types)) {
            $query->whereIn('Tipe Karyawan', $types);
        }

        return $query;
    }

    private function buildEmployeeOutQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = HrMasterEmployee::query();

        // Employee Out chart = "Aktif (Rentang Data) = N" — karyawan yang
        // SUDAH keluar s/d snapshot (end_date).
        // Logic: kebalikan dari Aktif (Rentang Data) = Y. Tgl Masuk <= end_date
        // DAN Aktif = N AND Valid From <= end_date.
        // Start date rentang_data diabaikan — yang penting cuma snapshot.
        $query->where('Aktif', 'N');

        // Snapshot filter dari Rentang Data — SELALU diaplikasikan bersamaan
        // dengan filter tanggal masuk & tanggal keluar (bukan else/fallback).
        if ($request->filled('rentang_data_to')) {
            $endDate = $request->get('rentang_data_to');
            $query->where('Tgl Masuk', '<=', $endDate)
                  ->where(function ($q) use ($endDate) {
                      $q->whereNull('Valid From')
                        ->orWhere('Valid From', '<=', $endDate);
                  });
        }

        // Filter "Tanggal Masuk" range — SELALU diaplikasikan.
        // Membatasi karyawan yang ditampilkan berdasarkan tgl masuk-nya,
        // meskipun rentang data juga diisi.
        if ($request->filled('tgl_masuk_from')) {
            $query->where('Tgl Masuk', '>=', $request->get('tgl_masuk_from'));
        }
        if ($request->filled('tgl_masuk_to')) {
            $query->where('Tgl Masuk', '<=', $request->get('tgl_masuk_to'));
        }

        // Filter "Tanggal Keluar" (Valid From) range — SELALU diaplikasikan.
        if ($request->filled('tgl_keluar_from')) {
            $query->where('Valid From', '>=', $request->get('tgl_keluar_from'));
        }
        if ($request->filled('tgl_keluar_to')) {
            $query->where('Valid From', '<=', $request->get('tgl_keluar_to'));
        }

        $depts = $this->getArrayFilter($request, 'departmen');
        if (!empty($depts)) {
            $query->whereIn('Departmen', $depts);
        }
        $subs = $this->getArrayFilter($request, 'sub_departmen');
        if (!empty($subs)) {
            $query->whereIn('Sub Departmen', $subs);
        }
        $types = $this->getArrayFilter($request, 'tipe_karyawan');
        if (!empty($types)) {
            $query->whereIn('Tipe Karyawan', $types);
        }

        return $query;
    }

    /**
     * Build data untuk chart "Monthly Total HeadCount - 2 Years".
     * Mengembalikan 24 bulan terakhir (rolling) dihitung per snapshot akhir bulan,
     * dengan logika "Aktif (Rentang Data) = Y" yang konsisten dengan Total HeadCount:
     *   - Tgl Masuk <= akhir bulan
     *   - DAN (Aktif = 'Y' ATAU (Aktif = 'N' AND (Valid From kosong OR Valid From > akhir bulan)))
     * End month = rentang_data_to (fallback: today). Start month = end - 23 bulan.
     * Filter Dept/Sub/Tipe + user access sudah di-handle oleh buildFilteredQuery.
     *
     * Implementasi: single SQL query dengan 24 SUM(CASE WHEN ...) aggregations.
     * MySQL handle 24 kolom aggregate sekaligus — O(1) PHP loop, O(N) DB scan.
     */
    private function buildHeadcountTrend(Request $request): array
    {
        $endDate = $request->filled('rentang_data_to')
            ? \Carbon\Carbon::parse($request->get('rentang_data_to'))->endOfDay()
            : now()->endOfDay();
        $startDate = (clone $endDate)->subMonths(23)->startOfMonth();

        // Bangun 24 SUM(CASE WHEN ...) aggregations
        $selects  = [];
        $bindings = [];
        $months   = [];
        $cursor   = (clone $startDate)->startOfMonth();
        $end      = (clone $endDate)->startOfMonth();
        $i        = 0;

        while ($cursor <= $end) {
            $monthEnd = (clone $cursor)->endOfMonth()->format('Y-m-d');
            $alias    = 'm' . $i;
            $selects[] = "SUM(CASE WHEN `Tgl Masuk` <= ? "
                . "AND (Aktif = 'Y' OR (Aktif = 'N' "
                . "AND (`Valid From` IS NULL OR `Valid From` > ?))) "
                . "THEN 1 ELSE 0 END) AS `{$alias}`";
            $bindings[] = $monthEnd;
            $bindings[] = $monthEnd;
            $months[]  = $cursor->format('Y-m');
            $cursor->addMonth();
            $i++;
        }

        // Single query dengan 24 kolom aggregate
        $row = $this->buildFilteredQuery($request)
            ->whereNotNull('Tgl Masuk')
            ->selectRaw(implode(', ', $selects), $bindings)
            ->first();

        $totals = [];
        for ($j = 0; $j < $i; $j++) {
            $totals[] = (int) ($row->{'m' . $j} ?? 0);
        }

        return [
            'months' => $months,
            'totals' => $totals,
        ];
    }

    /**
     * Ambil filter sebagai array. Support array (multiple) dan scalar (single).
     */
    private function getArrayFilter(Request $request, string $key): array
    {
        $val = $request->get($key);
        if (is_array($val)) {
            return array_filter($val, fn($v) => $v !== null && $v !== '');
        }
        if ($val === null || $val === '') {
            return [];
        }
        return [$val];
    }

    /**
     * Data untuk section Working Time & Overtime di dashboard.
     * Reuse filter Departmen/Sub Departmen/Tipe Karyawan (via join ke hr_master_employee).
     * Plus filter khusus WT&O: Rentang Tanggal Tgl In (optional).
     */
    public function wtoData(Request $request)
    {
        if (! $this->hasDashboardAccess()) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        // Apply user access restriction (dept/sub_dept yang diizinkan)
        $request = $this->sanitizeFilterRequest($request);

        $depts   = $this->getArrayFilter($request, 'departmen');
        $subs    = $this->getArrayFilter($request, 'sub_departmen');
        $types   = $this->getArrayFilter($request, 'tipe_karyawan');
        $nama    = $this->getArrayFilter($request, 'wto_nama');
        $tglFrom = $request->get('wto_tgl_in_from');
        $tglTo   = $request->get('wto_tgl_in_to');

        if (!empty($tglFrom) && empty($tglTo)) {
            $tglTo = $tglFrom;
        } elseif (!empty($tglTo) && empty($tglFrom)) {
            $tglFrom = $tglTo;
        }


        $base = DB::table('hr_workingtimeandovertime as wto')
            ->leftJoin('hr_master_employee as hme', 'hme.NIK', '=', 'wto.nik');

        if (!empty($depts)) {
            $base->whereIn('hme.Departmen', $depts);
        }
        if (!empty($subs)) {
            $base->whereIn(DB::raw('hme.`Sub Departmen`'), $subs);
        }
        if (!empty($types)) {
            $base->whereIn(DB::raw('hme.`Tipe Karyawan`'), $types);
        }
        if (!empty($nama)) {
            $base->whereIn('wto.nama', $nama);
        }
        if (!empty($tglFrom)) {
            $base->whereDate('wto.tgl_in', '>=', $tglFrom);
        }
        if (!empty($tglTo)) {
            $base->whereDate('wto.tgl_in', '<=', $tglTo);
        }

        $statsQuery = (clone $base);
        $totalJamSpkl      = (float) (clone $base)->sum('wto.jam_spkl');
        $totalJamHovt      = (float) (clone $base)->sum('wto.jam_hovt');
        $totalJamLembur    = $totalJamSpkl + $totalJamHovt;

        // Total Karyawan Lembur = sum dari nilai line chart (hari_kerja + hari_libur per bulan)
        $chartAggRows = (clone $base)
            ->select(
                DB::raw("COUNT(DISTINCT CASE WHEN wto.jam_spkl > 0 THEN wto.nik END) as hari_kerja"),
                DB::raw("COUNT(DISTINCT CASE WHEN wto.jam_hovt > 0 THEN wto.nik END) as hari_libur")
            )
            ->groupBy(DB::raw("DATE_FORMAT(wto.tgl_in, '%Y-%m')"))
            ->get();
        $totalKaryawanLembur = (int) $chartAggRows->sum('hari_kerja')
            + (int) $chartAggRows->sum('hari_libur');

        $perPage = (int) $request->get('per_page', 25);
        $perPage = max(1, min($perPage, 100));
        $page    = max(1, (int) $request->get('page', 1));

        $listQuery = clone $base;
        $totalList = (clone $listQuery)->count();
        $rows = $listQuery
            ->select(
                'wto.id',
                'wto.nik',
                'wto.nama',
                DB::raw('hme.Departmen as dept'),
                DB::raw('hme.`Sub Departmen` as sub_departmen'),
                'wto.section',
                'wto.tgl_in',
                'wto.jam_spkl',
                'wto.jam_hovt',
                'wto.no_spkl',
                'wto.send_by_username',
                'wto.updated_at'
            )
            ->orderBy('wto.tgl_in', 'desc')
            ->orderBy('wto.nik')
            ->forPage($page, $perPage)
            ->get();

        return response()->json([
            'stats' => [
                'total_jam_lembur'        => round($totalJamLembur, 2),
                'jam_lembur_hari_kerja'   => round($totalJamSpkl, 2),
                'jam_lembur_hari_libur'   => round($totalJamHovt, 2),
                'total_karyawan_lembur'   => $totalKaryawanLembur,
            ],
            'data' => $rows,
            'meta' => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $totalList,
                'last_page' => max(1, (int) ceil($totalList / $perPage)),
            ],
        ]);
    }

    /**
     * Export data WT&O ke CSV dengan filter yang sama dengan wtoData().
     * Stream sebagai file download.
     */
    public function wtoExport(Request $request)
    {
        if (! $this->hasDashboardAccess()) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        // Apply user access restriction
        $request = $this->sanitizeFilterRequest($request);

        $depts   = $this->getArrayFilter($request, 'departmen');
        $subs    = $this->getArrayFilter($request, 'sub_departmen');
        $types   = $this->getArrayFilter($request, 'tipe_karyawan');
        $nama    = $this->getArrayFilter($request, 'wto_nama');
        $tglFrom = $request->get('wto_tgl_in_from');
        $tglTo   = $request->get('wto_tgl_in_to');

        if (!empty($tglFrom) && empty($tglTo)) {
            $tglTo = $tglFrom;
        } elseif (!empty($tglTo) && empty($tglFrom)) {
            $tglFrom = $tglTo;
        }


        $base = DB::table('hr_workingtimeandovertime as wto')
            ->leftJoin('hr_master_employee as hme', 'hme.NIK', '=', 'wto.nik');

        if (!empty($depts)) {
            $base->whereIn('hme.Departmen', $depts);
        }
        if (!empty($subs)) {
            $base->whereIn(DB::raw('hme.`Sub Departmen`'), $subs);
        }
        if (!empty($types)) {
            $base->whereIn(DB::raw('hme.`Tipe Karyawan`'), $types);
        }
        if (!empty($nama)) {
            $base->whereIn('wto.nama', $nama);
        }
        if (!empty($tglFrom)) {
            $base->whereDate('wto.tgl_in', '>=', $tglFrom);
        }
        if (!empty($tglTo)) {
            $base->whereDate('wto.tgl_in', '<=', $tglTo);
        }

        $rows = $base
            ->select(
                'wto.nik',
                'wto.nama',
                DB::raw('hme.Departmen as dept'),
                DB::raw('hme.`Sub Departmen` as sub_departmen'),
                'wto.section',
                'wto.tgl_in',
                'wto.jam_spkl',
                'wto.jam_hovt',
                'wto.no_spkl',
                'wto.send_by_username',
                'wto.created_at',
                'wto.updated_at'
            )
            ->orderBy('wto.tgl_in', 'desc')
            ->orderBy('wto.nik')
            ->get();

        $filename = 'wt_overtime_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            'Pragma'              => 'no-cache',
        ];

        $callback = function () use ($rows) {
            $fh = fopen('php://output', 'w');
            fwrite($fh, "\xEF\xBB\xBF");
            fputcsv($fh, [
                'NIK', 'Nama', 'Departmen', 'Sub Departmen', 'Section',
                'Tgl In', 'Jam SPKL', 'Jam HOVT', 'Jam Lembur',
                'No SPKL', 'Send By', 'Created At', 'Updated At',
            ]);
            foreach ($rows as $r) {
                $spkl = (float) ($r->jam_spkl ?? 0);
                $hovt = (float) ($r->jam_hovt ?? 0);
                fputcsv($fh, [
                    $r->nik,
                    $r->nama,
                    $r->dept,
                    $r->sub_departmen,
                    $r->section,
                    $r->tgl_in,
                    $spkl,
                    $hovt,
                    $spkl + $hovt,
                    $r->no_spkl,
                    $r->send_by_username,
                    $r->created_at,
                    $r->updated_at,
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Line chart data: jumlah karyawan lembur (Hari Kerja) per bulan.
     * Apply same filters as wtoData. Range dinamis: pakai Tgl In filter
     * jika ada, atau min/max tgl_in dari data ter-filter.
     */
    public function wtoChartData(Request $request)
    {
        if (! $this->hasDashboardAccess()) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        // Apply user access restriction
        $request = $this->sanitizeFilterRequest($request);

        $depts   = $this->getArrayFilter($request, 'departmen');
        $subs    = $this->getArrayFilter($request, 'sub_departmen');
        $types   = $this->getArrayFilter($request, 'tipe_karyawan');
        $nama    = $this->getArrayFilter($request, 'wto_nama');
        $tglFrom = $request->get('wto_tgl_in_from');
        $tglTo   = $request->get('wto_tgl_in_to');

        if (!empty($tglFrom) && empty($tglTo)) {
            $tglTo = $tglFrom;
        } elseif (!empty($tglTo) && empty($tglFrom)) {
            $tglFrom = $tglTo;
        }


        $base = DB::table('hr_workingtimeandovertime as wto')
            ->leftJoin('hr_master_employee as hme', 'hme.NIK', '=', 'wto.nik');

        if (!empty($depts)) {
            $base->whereIn('hme.Departmen', $depts);
        }
        if (!empty($subs)) {
            $base->whereIn(DB::raw('hme.`Sub Departmen`'), $subs);
        }
        if (!empty($types)) {
            $base->whereIn(DB::raw('hme.`Tipe Karyawan`'), $types);
        }
        if (!empty($nama)) {
            $base->whereIn('wto.nama', $nama);
        }
        if (!empty($tglFrom)) {
            $base->whereDate('wto.tgl_in', '>=', $tglFrom);
        }
        if (!empty($tglTo)) {
            $base->whereDate('wto.tgl_in', '<=', $tglTo);
        }

        $rows = (clone $base)
            ->select(
                DB::raw("DATE_FORMAT(wto.tgl_in, '%Y-%m') as ym"),
                DB::raw("COALESCE(hme.Departmen, 'Unknown') as dept"),
                DB::raw("COUNT(DISTINCT CASE WHEN wto.jam_spkl > 0 THEN wto.nik END) as hari_kerja"),
                DB::raw("COUNT(DISTINCT CASE WHEN wto.jam_hovt > 0 THEN wto.nik END) as hari_libur"),
                DB::raw("SUM(CASE WHEN wto.jam_spkl > 0 THEN wto.jam_spkl ELSE 0 END) as jam_kerja"),
                DB::raw("SUM(CASE WHEN wto.jam_hovt > 0 THEN wto.jam_hovt ELSE 0 END) as jam_libur")
            )
            ->groupBy('ym', 'hme.Departmen')
            ->orderBy('ym')
            ->orderBy('hme.Departmen')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'months'            => [],
                'hari_kerja'        => [],
                'hari_libur'        => [],
                'jam_kerja'         => [],
                'jam_libur'         => [],
                'departments'       => [],
                'department_series' => [],
                'range_start'       => null,
                'range_end'         => null,
            ]);
        }

        $departments = $rows->pluck('dept')->unique()->sort()->values()->toArray();

        $dataStart = $rows->first()->ym;
        $dataEnd   = $rows->last()->ym;
        $startYm   = $tglFrom ? substr($tglFrom, 0, 7) : $dataStart;
        $endYm     = $tglTo   ? substr($tglTo,   0, 7) : $dataEnd;

        $allMonths = [];
        $cursor = new \DateTime($startYm . '-01');
        $end    = new \DateTime($endYm   . '-01');
        while ($cursor <= $end) {
            $allMonths[] = $cursor->format('Y-m');
            $cursor->modify('+1 month');
        }

        $aggregate = [];
        $deptSeries = [];
        foreach ($allMonths as $m) {
            $aggregate[$m] = ['hari_kerja' => 0, 'hari_libur' => 0, 'jam_kerja' => 0, 'jam_libur' => 0];
            foreach ($departments as $d) {
                if (!isset($deptSeries[$d])) {
                    $deptSeries[$d] = [];
                }
                $deptSeries[$d][$m] = ['jam_kerja' => 0, 'jam_libur' => 0];
            }
        }

        foreach ($rows as $r) {
            $aggregate[$r->ym]['hari_kerja'] += (int) $r->hari_kerja;
            $aggregate[$r->ym]['hari_libur'] += (int) $r->hari_libur;
            $aggregate[$r->ym]['jam_kerja']  += (float) $r->jam_kerja;
            $aggregate[$r->ym]['jam_libur']  += (float) $r->jam_libur;

            if (isset($deptSeries[$r->dept][$r->ym])) {
                $deptSeries[$r->dept][$r->ym]['jam_kerja'] = (float) $r->jam_kerja;
                $deptSeries[$r->dept][$r->ym]['jam_libur'] = (float) $r->jam_libur;
            }
        }

        $hariKerjaArr = array_map(fn($v) => $v['hari_kerja'], array_values($aggregate));
        $hariLiburArr = array_map(fn($v) => $v['hari_libur'], array_values($aggregate));
        $jamKerjaArr  = array_map(fn($v) => $v['jam_kerja'],  array_values($aggregate));
        $jamLiburArr  = array_map(fn($v) => $v['jam_libur'],  array_values($aggregate));

        $deptSeriesResult = [];
        foreach ($departments as $d) {
            $jk = [];
            $jl = [];
            foreach ($allMonths as $m) {
                $jk[] = $deptSeries[$d][$m]['jam_kerja'];
                $jl[] = $deptSeries[$d][$m]['jam_libur'];
            }
            $deptSeriesResult[$d] = [
                'jam_kerja' => $jk,
                'jam_libur' => $jl,
            ];
        }

        return response()->json([
            'months'            => $allMonths,
            'hari_kerja'        => $hariKerjaArr,
            'hari_libur'        => $hariLiburArr,
            'jam_kerja'         => $jamKerjaArr,
            'jam_libur'         => $jamLiburArr,
            'departments'       => $departments,
            'department_series' => $deptSeriesResult,
            'range_start'       => $startYm,
            'range_end'         => $endYm,
        ]);
    }

    /**
     * Top 10 karyawan dengan total jam lembur (SPKL + HOVT) terbesar.
     * Apply same filters as wtoData (Dept, Sub Dept, Tipe, Tgl In)
     * dan user access restrictions.
     */
    public function wtoTopLembur(Request $request)
    {
        if (! $this->hasTopLemburAccess()) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $request = $this->sanitizeFilterRequest($request);

        $depts   = $this->getArrayFilter($request, 'departmen');
        $subs    = $this->getArrayFilter($request, 'sub_departmen');
        $types   = $this->getArrayFilter($request, 'tipe_karyawan');
        $nama    = $this->getArrayFilter($request, 'wto_nama');
        $tglFrom = $request->get('wto_tgl_in_from');
        $tglTo   = $request->get('wto_tgl_in_to');

        if (!empty($tglFrom) && empty($tglTo)) {
            $tglTo = $tglFrom;
        } elseif (!empty($tglTo) && empty($tglFrom)) {
            $tglFrom = $tglTo;
        }


        $base = DB::table('hr_workingtimeandovertime as wto')
            ->leftJoin('hr_master_employee as hme', 'hme.NIK', '=', 'wto.nik');

        if (!empty($depts)) {
            $base->whereIn('hme.Departmen', $depts);
        }
        if (!empty($subs)) {
            $base->whereIn(DB::raw('hme.`Sub Departmen`'), $subs);
        }
        if (!empty($types)) {
            $base->whereIn(DB::raw('hme.`Tipe Karyawan`'), $types);
        }
        if (!empty($nama)) {
            $base->whereIn('wto.nama', $nama);
        }
        if (!empty($tglFrom)) {
            $base->whereDate('wto.tgl_in', '>=', $tglFrom);
        }
        if (!empty($tglTo)) {
            $base->whereDate('wto.tgl_in', '<=', $tglTo);
        }

        $rows = (clone $base)
            ->select(
                'wto.nik',
                'wto.nama',
                DB::raw('hme.Departmen as dept'),
                DB::raw('hme.`Sub Departmen` as sub_departmen'),
                DB::raw('SUM(wto.jam_spkl) as total_spkl'),
                DB::raw('SUM(wto.jam_hovt) as total_hovt'),
                DB::raw('SUM(wto.jam_spkl) + SUM(wto.jam_hovt) as total_lembur'),
                DB::raw('COUNT(*) as total_records')
            )
            ->groupBy('wto.nik', 'wto.nama', DB::raw('hme.Departmen'), DB::raw('hme.`Sub Departmen`'))
            ->orderByDesc('total_lembur')
            ->orderBy('wto.nik')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $rows,
        ]);
    }

    /**
     * Return distinct nama karyawan yang ada di data lembur,
     * difilter berdasarkan Dept/Sub Dept/Tipe Karyawan (Tgl In tidak ikut).
     * Dipakai untuk populate dropdown filter Nama di WT&O dashboard.
     * Apply user access restriction yang sama dengan endpoint lain.
     */
    public function wtoNames(Request $request)
    {
        if (! $this->hasDashboardAccess()) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $request = $this->sanitizeFilterRequest($request);

        $depts = $this->getArrayFilter($request, 'departmen');
        $subs  = $this->getArrayFilter($request, 'sub_departmen');
        $types = $this->getArrayFilter($request, 'tipe_karyawan');

        $base = DB::table('hr_workingtimeandovertime as wto')
            ->leftJoin('hr_master_employee as hme', 'hme.NIK', '=', 'wto.nik');

        if (!empty($depts)) {
            $base->whereIn('hme.Departmen', $depts);
        }
        if (!empty($subs)) {
            $base->whereIn(DB::raw('hme.`Sub Departmen`'), $subs);
        }
        if (!empty($types)) {
            $base->whereIn(DB::raw('hme.`Tipe Karyawan`'), $types);
        }

        $names = (clone $base)
            ->select('wto.nama')
            ->distinct()
            ->orderBy('wto.nama')
            ->pluck('wto.nama')
            ->filter()
            ->values();

        return response()->json([
            'names' => $names,
            'total' => $names->count(),
        ]);
    }
}
