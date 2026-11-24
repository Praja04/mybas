<?php

namespace App\Support;

use App\Models\HR\HrIppManagerMapping;
use App\Models\HR\HrPkAssociateManagerMapping;
use App\Models\HR\HrPkOtorisasi;
use Illuminate\Support\Facades\DB;

class PkAtasan
{
    private $mappings = null;

    private array $mgrByDept = [];

    private array $mgrBySub = [];

    private array $amBySub = [];

    private array $amBySection = [];

    private array $amByNik = [];

    private array $otoByNik = [];

    private array $otoRows = [];

    private bool $built = false;

    private function build(): void
    {
        if ($this->built) {
            return;
        }
        $this->built = true;

        $this->mappings = HrIppManagerMapping::all()->keyBy(function ($m) {
            return strtolower(trim((string) $m->sub_departmen));
        });

        $userNikSet = array_flip(
            DB::table('users')->whereNotNull('nik')->where('nik', '!=', '')->pluck('nik')->all()
        );

        foreach (HrPkAssociateManagerMapping::all() as $am) {
            $key = strtolower(trim((string) $am->nik));
            $amNik = trim((string) $am->nik_associate_manager);
            if ($key !== '' && $amNik !== '' && isset($userNikSet[$amNik])) {
                $this->amByNik[$key] = ['nik' => $amNik, 'nama' => $am->nama_associate_manager, 'source' => 'mapping'];
            }
        }

        $mgrs = DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereRaw("Level IN ('8', '9', '10', '11', '12', '13')")
            ->orderByRaw('CAST(Level AS UNSIGNED) ASC, NIK ASC')
            ->get(['NIK', 'Nama', 'Departmen', 'Sub Departmen']);
        foreach ($mgrs as $m) {
            if (!isset($userNikSet[$m->NIK])) {
                continue;
            }
            $dept = strtolower(trim((string) $m->Departmen));
            $sub = strtolower(trim((string) $m->{'Sub Departmen'}));
            $cand = ['nik' => $m->NIK, 'nama' => $m->Nama, 'source' => 'auto'];
            if ($dept !== '' && !isset($this->mgrByDept[$dept])) {
                $this->mgrByDept[$dept] = $cand;
            }
            if ($sub !== '' && !isset($this->mgrBySub[$sub])) {
                $this->mgrBySub[$sub] = $cand;
            }
        }

        $ams = DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereRaw("Level IN ('6', '7')")
            ->orderByRaw('CAST(Level AS UNSIGNED) DESC, NIK ASC')
            ->get(['NIK', 'Nama', 'Departmen', 'Sub Departmen', 'Section']);
        foreach ($ams as $m) {
            if (!isset($userNikSet[$m->NIK])) {
                continue;
            }
            $cand = ['nik' => $m->NIK, 'nama' => $m->Nama, 'source' => 'auto'];
            if (strtolower(trim((string) $m->Departmen)) === 'factory') {
                $section = strtolower(trim((string) $m->Section));
                if ($section === '') {
                    continue;
                }
                if (count($this->amBySection[$section] ?? []) < 2) {
                    $this->amBySection[$section][] = $cand;
                }
                continue;
            }
            $sub = strtolower(trim((string) $m->{'Sub Departmen'}));
            if ($sub === '') {
                continue;
            }
            if (count($this->amBySub[$sub] ?? []) < 2) {
                $this->amBySub[$sub][] = $cand;
            }
        }

        $otoRows = HrPkOtorisasi::orderBy('id')->get();
        if ($otoRows->isNotEmpty()) {
            $niks = $otoRows->pluck('nik_yang_bersangkutan')
                ->map(fn ($n) => trim((string) $n))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $emps = DB::table('hr_master_employee')
                ->whereIn('NIK', $niks)
                ->get(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level'])
                ->keyBy(fn ($e) => strtolower(trim((string) $e->NIK)));

            foreach ($otoRows as $o) {
                $nik = trim((string) $o->nik_yang_bersangkutan);
                $key = strtolower($nik);
                if ($key === '') {
                    continue;
                }
                $emp = $emps->get($key);
                $mgrNik = trim((string) $o->nik_manager);
                $amNik = trim((string) $o->nik_associate_manager);
                $this->otoByNik[$key] = [
                    'manager' => $mgrNik !== ''
                        ? ['nik' => $mgrNik, 'nama' => $o->nama_manager, 'source' => 'otorisasi']
                        : null,
                    'associate_manager' => $amNik !== ''
                        ? ['nik' => $amNik, 'nama' => $o->nama_associate_manager, 'source' => 'otorisasi']
                        : null,
                ];
                $this->otoRows[] = [
                    'nik' => $nik,
                    'nama' => $emp ? $emp->Nama : ($o->nama_yang_bersangkutan ?? ''),
                    'jabatan' => $emp ? $emp->Jabatan : '-',
                    'departemen' => $emp ? trim((string) $emp->Departmen) : '',
                    'sub_departemen' => $emp ? trim((string) ($emp->{'Sub Departmen'} ?? '')) : '',
                    'section' => $emp ? trim((string) ($emp->Section ?? '')) : '',
                    'level' => $emp ? (int) $emp->Level : 0,
                    'kategori' => trim((string) ($o->kategori ?? 'spv')),
                    'manager_nik' => $mgrNik,
                    'associate_manager_nik' => $amNik,
                ];
            }
        }
    }

    public function resolveAll(object $emp): array
    {
        return [
            'manager' => $this->resolveManager($emp),
            'associate_manager' => $this->resolveAssociateManager($emp),
        ];
    }

    public function teamOf(string $nik): array
    {
        $nik = trim($nik);
        if ($nik === '') {
            return [];
        }

        $emps = DB::table('hr_master_employee')
            ->where('Aktif', 'Y')
            ->whereRaw('Level IN (\'2\', \'3\', \'4\', \'5\', \'6\', \'7\')')
            ->orderByRaw('CAST(Level AS UNSIGNED) ASC, NIK ASC')
            ->get(['NIK', 'Nama', 'Jabatan', 'Departmen', 'Sub Departmen', 'Section', 'Level']);

        $team = [];
        foreach ($emps as $emp) {
            if ($emp->NIK === $nik) {
                continue;
            }
            $atasan = $this->resolveAll($emp);
            $role = null;
            if (($atasan['associate_manager']['nik'] ?? '') === $nik) {
                $role = 'associate_manager';
            } elseif (($atasan['manager']['nik'] ?? '') === $nik) {
                $role = 'manager';
            }
            if (!$role) {
                continue;
            }

            $level = (int) $emp->Level;
            $team[] = [
                'nik' => $emp->NIK,
                'nama' => $emp->Nama,
                'jabatan' => $emp->Jabatan,
                'departemen' => trim((string) $emp->Departmen),
                'sub_departemen' => trim((string) ($emp->{'Sub Departmen'} ?? '')),
                'section' => trim((string) ($emp->Section ?? '')),
                'level' => $level,
                'group' => PmsEmployeeSnapshot::groupOf($level),
                'role' => $role,
                'has_am' => trim((string) ($atasan['associate_manager']['nik'] ?? '')) !== '',
            ];
        }

        $this->build();

        $seen = [];
        foreach ($team as $m) {
            $seen[$m['nik']] = true;
        }

        foreach ($this->otoRows as $o) {
            if ($o['nik'] === $nik || isset($seen[$o['nik']])) {
                continue;
            }
            $role = null;
            if ($o['associate_manager_nik'] === $nik) {
                $role = 'associate_manager';
            } elseif ($o['manager_nik'] === $nik) {
                $role = 'manager';
            }
            if (!$role) {
                continue;
            }
            $level = (int) $o['level'];
            $kategori = trim((string) ($o['kategori'] ?? 'spv'));
            $group = $kategori === 'foreman'
                ? (PmsEmployeeSnapshot::groupOf($level) ?? '2-3')
                : ($level >= 6 ? '6-7' : '4-5');
            $team[] = [
                'nik' => $o['nik'],
                'nama' => $o['nama'],
                'jabatan' => $o['jabatan'],
                'departemen' => $o['departemen'],
                'sub_departemen' => $o['sub_departemen'],
                'section' => $o['section'],
                'level' => $level,
                'group' => $group,
                'role' => $role,
                'has_am' => trim((string) $o['associate_manager_nik']) !== '',
            ];
        }

        return $team;
    }

    public function resolveManager(object $emp): ?array
    {
        $this->build();

        $oto = $this->otoByNik[strtolower(trim((string) ($emp->NIK ?? '')))] ?? null;
        if ($oto && $oto['manager']) {
            return $oto['manager'];
        }

        $dept = trim((string) ($emp->Departmen ?? ''));
        $sub = trim((string) ($emp->{'Sub Departmen'} ?? ''));

        $map = ($sub !== '') ? ($this->mappings[strtolower($sub)] ?? null) : null;
        if ($map && trim((string) $map->nik_manager) !== '') {
            return [
                'nik' => trim((string) $map->nik_manager),
                'nama' => trim((string) $map->nama_manager),
                'source' => 'mapping',
            ];
        }

        if (strtolower($dept) === 'factory' && $sub !== '') {
            return $this->mgrBySub[strtolower($sub)] ?? null;
        }
        if ($dept !== '') {
            return $this->mgrByDept[strtolower($dept)] ?? null;
        }

        return null;
    }

    public function resolveAssociateManager(object $emp): ?array
    {
        $this->build();

        $oto = $this->otoByNik[strtolower(trim((string) ($emp->NIK ?? '')))] ?? null;
        if ($oto !== null) {
            return $oto['associate_manager'];
        }

        $mapped = $this->amByNik[strtolower(trim((string) ($emp->NIK ?? '')))] ?? null;
        if ($mapped) {
            return $mapped;
        }

        if ((int) ($emp->Level ?? 0) >= 6) {
            return null;
        }

        if (strtolower(trim((string) ($emp->Departmen ?? ''))) === 'factory') {
            $section = trim((string) ($emp->Section ?? ''));
            if ($section === '') {
                return null;
            }
            foreach ($this->amBySection[strtolower($section)] ?? [] as $cand) {
                if ($cand['nik'] !== trim((string) $emp->NIK)) {
                    return $cand;
                }
            }

            return null;
        }

        $sub = trim((string) ($emp->{'Sub Departmen'} ?? ''));
        if ($sub === '') {
            return null;
        }

        foreach ($this->amBySub[strtolower($sub)] ?? [] as $cand) {
            if ($cand['nik'] !== trim((string) $emp->NIK)) {
                return $cand;
            }
        }

        return null;
    }
}
