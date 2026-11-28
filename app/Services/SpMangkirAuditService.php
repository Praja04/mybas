<?php

namespace App\Services;

use App\HrKaryawan;
use App\SpPelanggaran;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SpMangkirAuditService
{
    /**
     * Audit an attendance Excel file against SP Mangkir database records.
     *
     * @param string $filePath Absolute path to uploaded Excel file
     * @param array $targetCodes Array of attendance codes to audit (default ['A', 'ALFA', 'MANGKIR'])
     * @return array Audit statistics and comparison records
     */
    public function auditExcelFile(string $filePath, array $targetCodes = ['A', 'ALFA', 'MANGKIR']): array
    {
        $spreadsheet = IOFactory::load($filePath);

        $targetCodesUpper = array_map(function ($c) {
            return strtoupper(trim($c));
        }, $targetCodes);

        $allExcelEntries = [];
        $monthKey = null;
        $periodFormatted = null;
        $year = (int)date('Y');
        $month = (int)date('m');

        // Iterate through all sheets to collect matching entries
        foreach ($spreadsheet->getAllSheets() as $sheetIndex => $sheet) {
            $highestRow = $sheet->getHighestRow();
            $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

            // Extract period from cell A2 or B2 or title if not yet determined
            if (!$monthKey) {
                $periodString = trim((string)$sheet->getCell('A2')->getValue());
                if (empty($periodString)) {
                    $periodString = trim((string)$sheet->getCell('B2')->getValue());
                }
                $periodInfo = $this->parsePeriodString($periodString);
                $year = $periodInfo['year'];
                $month = $periodInfo['month'];
                $monthKey = sprintf('%04d-%02d', $year, $month);
                $periodFormatted = Carbon::createFromDate($year, $month, 1)->isoFormat('MMMM YYYY');
            }

            // Locate Header Row (usually Row 6)
            $headerRowIndex = 6;
            for ($r = 1; $r <= 12; $r++) {
                $v = strtoupper(trim((string)$sheet->getCellByColumnAndRow(1, $r)->getValue()));
                if ($v === 'NIK') {
                    $headerRowIndex = $r;
                    break;
                }
            }

            // Map column indices
            $nikCol = 1;
            $namaCol = 2;
            $deptCol = 3;
            $sectionCol = 4;
            $dayCols = [];

            for ($c = 1; $c <= $highestColIndex; $c++) {
                $headerVal = trim((string)$sheet->getCellByColumnAndRow($c, $headerRowIndex)->getValue());
                $upperH = strtoupper($headerVal);

                if ($upperH === 'NIK') $nikCol = $c;
                elseif ($upperH === 'NAMA') $namaCol = $c;
                elseif (in_array($upperH, ['DEPARTMENT', 'DEPARTEMEN', 'DIVISI'])) $deptCol = $c;
                elseif (in_array($upperH, ['SECTION', 'BAGIAN'])) $sectionCol = $c;
                else {
                    $cleanDay = preg_replace('/[^0-9]/', '', $headerVal);
                    if (is_numeric($cleanDay) && (int)$cleanDay >= 1 && (int)$cleanDay <= 31 && strlen($cleanDay) <= 2) {
                        $dayCols[(int)$cleanDay] = $c;
                    }
                }
            }

            if (empty($dayCols)) {
                continue;
            }

            $ignoredNiks = ['KETERANGAN', 'L1', 'L2', 'L3', 'A', 'CDC1', 'CHJ', 'CIM', 'CM', 'CUT', 'ITB', 'IVD', 'KD', 'SKK', 'DOWNLOADED TIME', 'TOTAL', 'SUMMARY'];

            for ($r = $headerRowIndex + 1; $r <= $highestRow; $r++) {
                $rawNik = trim((string)$sheet->getCellByColumnAndRow($nikCol, $r)->getValue());
                if (empty($rawNik)) continue;

                $nik = preg_replace('/\.0$/', '', $rawNik);
                if (in_array(strtoupper($nik), $ignoredNiks, true)) continue;

                $nama = trim((string)$sheet->getCellByColumnAndRow($namaCol, $r)->getValue());
                $dept = trim((string)$sheet->getCellByColumnAndRow($deptCol, $r)->getValue());
                $section = trim((string)$sheet->getCellByColumnAndRow($sectionCol, $r)->getValue());

                foreach ($dayCols as $dayNum => $colIdx) {
                    $cellVal = strtoupper(trim((string)$sheet->getCellByColumnAndRow($colIdx, $r)->getValue()));

                    if (in_array($cellVal, $targetCodesUpper, true)) {
                        $tanggal = sprintf('%04d-%02d-%02d', $year, $month, $dayNum);
                        $entryKey = $nik . '|' . $tanggal;

                        if (!isset($allExcelEntries[$entryKey])) {
                            $allExcelEntries[$entryKey] = [
                                'nik' => $nik,
                                'nama' => $nama,
                                'dept' => $dept,
                                'section' => $section,
                                'day_num' => $dayNum,
                                'tanggal' => $tanggal,
                                'code' => $cellVal,
                            ];
                        }
                    }
                }
            }

            // If we found records in sheet 0 or sheet 1, break to avoid duplicating from filtered sheet summaries
            if (count($allExcelEntries) > 0) {
                break;
            }
        }

        if (!$monthKey) {
            $monthKey = date('Y-m');
            $periodFormatted = Carbon::now()->isoFormat('MMMM YYYY');
        }

        $excelMangkirEntries = array_values($allExcelEntries);

        // Fetch DB employees and SP Mangkir records
        $allNiks = array_unique(array_column($excelMangkirEntries, 'nik'));

        // Query employees by exact NIK or padded NIK
        $employees = HrKaryawan::whereIn('nik', $allNiks)->get()->keyBy(function ($item) {
            return (string)$item->nik;
        });

        $empIds = $employees->pluck('id')->toArray();

        // Existing SP Mangkir records for these employees in this month
        $existingSps = SpPelanggaran::with(['employee', 'dates', 'creator'])
            ->where('sumber_data', 'MANGKIR')
            ->whereIn('employee_id', $empIds)
            ->where('bulan_mangkir', $monthKey)
            ->whereNotIn('current_status', [SpPelanggaran::STATUS_CANCELLED, SpPelanggaran::STATUS_REJECTED])
            ->get();

        // Index existing SPs by employee_id + date
        $spIndex = [];
        foreach ($existingSps as $sp) {
            $empId = $sp->employee_id;
            foreach ($sp->dates as $spDate) {
                $key = $empId . '|' . $spDate->tanggal;
                $spIndex[$key] = $sp;
            }
        }

        // Cross-reference audit results
        $auditRecords = [];
        $totalSudahInput = 0;
        $totalBelumInput = 0;
        $empMonthlyExcelCount = [];

        foreach ($excelMangkirEntries as $entry) {
            $nik = $entry['nik'];
            $tanggal = $entry['tanggal'];
            $emp = $employees[$nik] ?? null;

            $empMonthlyExcelCount[$nik] = ($empMonthlyExcelCount[$nik] ?? 0) + 1;
            $mangkirKeExcel = $empMonthlyExcelCount[$nik];

            if ($emp) {
                $key = $emp->id . '|' . $tanggal;
                $existingSp = $spIndex[$key] ?? null;

                if ($existingSp) {
                    $totalSudahInput++;
                    $auditRecords[] = [
                        'status_audit' => 'SUDAH_INPUT',
                        'nik' => $nik,
                        'nama' => $emp->nama,
                        'dept' => $emp->kode_divisi ?: ($emp->kode_bagian ?: $entry['dept']),
                        'section' => $entry['section'],
                        'tanggal' => $tanggal,
                        'tanggal_formatted' => Carbon::parse($tanggal)->isoFormat('D MMMM YYYY'),
                        'mangkir_ke' => $existingSp->mangkir_ke ?? $mangkirKeExcel,
                        'code' => $entry['code'],
                        'employee_id' => $emp->id,
                        'sp_id' => $existingSp->id,
                        'nomor_sp' => $existingSp->nomor_sp_generated ?: ($existingSp->kode_admin ?: 'SP Draft'),
                        'current_status' => $existingSp->current_status,
                        'creator_name' => $existingSp->creator ? $existingSp->creator->name : '-',
                        'created_at' => $existingSp->created_at ? $existingSp->created_at->format('d/m/Y H:i') : '-',
                    ];
                } else {
                    $totalBelumInput++;
                    $auditRecords[] = [
                        'status_audit' => 'BELUM_INPUT',
                        'nik' => $nik,
                        'nama' => $emp->nama,
                        'dept' => $emp->kode_divisi ?: ($emp->kode_bagian ?: $entry['dept']),
                        'section' => $entry['section'],
                        'tanggal' => $tanggal,
                        'tanggal_formatted' => Carbon::parse($tanggal)->isoFormat('D MMMM YYYY'),
                        'mangkir_ke' => $mangkirKeExcel,
                        'code' => $entry['code'],
                        'employee_id' => $emp->id,
                        'sp_id' => null,
                        'nomor_sp' => null,
                        'current_status' => null,
                        'creator_name' => null,
                        'created_at' => null,
                    ];
                }
            } else {
                $totalBelumInput++;
                $auditRecords[] = [
                    'status_audit' => 'NOT_FOUND_IN_DB',
                    'nik' => $nik,
                    'nama' => $entry['nama'],
                    'dept' => $entry['dept'],
                    'section' => $entry['section'],
                    'tanggal' => $tanggal,
                    'tanggal_formatted' => Carbon::parse($tanggal)->isoFormat('D MMMM YYYY'),
                    'mangkir_ke' => $mangkirKeExcel,
                    'code' => $entry['code'],
                    'employee_id' => null,
                    'sp_id' => null,
                    'nomor_sp' => null,
                    'current_status' => null,
                    'creator_name' => null,
                    'created_at' => null,
                ];
            }
        }

        return [
            'period_key' => $monthKey,
            'period_formatted' => $periodFormatted,
            'target_codes' => $targetCodesUpper,
            'total_excel_mangkir' => count($excelMangkirEntries),
            'total_sudah_input' => $totalSudahInput,
            'total_belum_input' => $totalBelumInput,
            'total_karyawan_mangkir' => count($allNiks),
            'audit_records' => $auditRecords,
        ];
    }

    /**
     * Save raw parsed audit mangkir entries into database table `sp_audit_mangkirs`.
     */
    public function saveAuditRecordsToDb(array $auditResult, ?string $filename = null, ?int $userId = null): void
    {
        $periodKey = $auditResult['period_key'];
        $periodFormatted = $auditResult['period_formatted'];
        $auditRecords = $auditResult['audit_records'] ?? [];

        // Delete existing audit records for this period if overwrite is triggered
        \App\SpAuditMangkir::where('periode', $periodKey)->delete();

        $rows = [];
        $now = Carbon::now();

        foreach ($auditRecords as $rec) {
            $rows[] = [
                'periode' => $periodKey,
                'periode_label' => $periodFormatted,
                'employee_id' => $rec['employee_id'] ?? null,
                'nik' => $rec['nik'],
                'nama' => $rec['nama'],
                'department' => $rec['dept'] ?? null,
                'section' => $rec['section'] ?? null,
                'tanggal' => $rec['tanggal'],
                'mangkir_ke' => $rec['mangkir_ke'] ?? 1,
                'kode_absensi' => $rec['code'] ?? 'A',
                'filename' => $filename,
                'created_by_user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            foreach (array_chunk($rows, 100) as $chunk) {
                \App\SpAuditMangkir::insert($chunk);
            }
        }
    }

    /**
     * Audit mangkir records stored in `sp_audit_mangkirs` table against `sp_pelanggarans`.
     */
    public function auditFromDatabase(string $periodKey): array
    {
        $dbRecords = \App\SpAuditMangkir::where('periode', $periodKey)->get();

        if ($dbRecords->isEmpty()) {
            return [];
        }

        $periodFormatted = $dbRecords->first()->periode_label ?? $periodKey;
        $allNiks = $dbRecords->pluck('nik')->unique()->toArray();

        $employees = HrKaryawan::whereIn('nik', $allNiks)->get()->keyBy(function ($item) {
            return (string)$item->nik;
        });

        $empIds = $employees->pluck('id')->toArray();

        $existingSps = SpPelanggaran::with(['employee', 'dates', 'creator'])
            ->where('sumber_data', 'MANGKIR')
            ->whereIn('employee_id', $empIds)
            ->where('bulan_mangkir', $periodKey)
            ->whereNotIn('current_status', [SpPelanggaran::STATUS_CANCELLED, SpPelanggaran::STATUS_REJECTED])
            ->get();

        $spIndex = [];
        foreach ($existingSps as $sp) {
            $empId = $sp->employee_id;
            foreach ($sp->dates as $spDate) {
                $key = $empId . '|' . $spDate->tanggal;
                $spIndex[$key] = $sp;
            }
        }

        $auditRecords = [];
        $totalSudahInput = 0;
        $totalBelumInput = 0;
        $targetCodes = [];

        foreach ($dbRecords as $entry) {
            $nik = $entry->nik;
            $tanggal = $entry->tanggal;
            $emp = $employees[$nik] ?? null;
            $code = $entry->kode_absensi;
            if (!in_array($code, $targetCodes)) $targetCodes[] = $code;

            if ($emp) {
                $key = $emp->id . '|' . $tanggal;
                $existingSp = $spIndex[$key] ?? null;

                if ($existingSp) {
                    $totalSudahInput++;
                    $auditRecords[] = [
                        'status_audit' => 'SUDAH_INPUT',
                        'nik' => $nik,
                        'nama' => $emp->nama,
                        'dept' => $emp->kode_divisi ?: ($emp->kode_bagian ?: $entry->department),
                        'section' => $entry->section,
                        'tanggal' => $tanggal,
                        'tanggal_formatted' => Carbon::parse($tanggal)->isoFormat('D MMMM YYYY'),
                        'mangkir_ke' => $existingSp->mangkir_ke ?? $entry->mangkir_ke,
                        'code' => $code,
                        'employee_id' => $emp->id,
                        'sp_id' => $existingSp->id,
                        'nomor_sp' => $existingSp->nomor_sp_generated ?: ($existingSp->kode_admin ?: 'SP Draft'),
                        'current_status' => $existingSp->current_status,
                        'creator_name' => $existingSp->creator ? $existingSp->creator->name : '-',
                        'created_at' => $existingSp->created_at ? $existingSp->created_at->format('d/m/Y H:i') : '-',
                    ];
                } else {
                    $totalBelumInput++;
                    $auditRecords[] = [
                        'status_audit' => 'BELUM_INPUT',
                        'nik' => $nik,
                        'nama' => $emp->nama,
                        'dept' => $emp->kode_divisi ?: ($emp->kode_bagian ?: $entry->department),
                        'section' => $entry->section,
                        'tanggal' => $tanggal,
                        'tanggal_formatted' => Carbon::parse($tanggal)->isoFormat('D MMMM YYYY'),
                        'mangkir_ke' => $entry->mangkir_ke,
                        'code' => $code,
                        'employee_id' => $emp->id,
                        'sp_id' => null,
                        'nomor_sp' => null,
                        'current_status' => null,
                        'creator_name' => null,
                        'created_at' => null,
                    ];
                }
            } else {
                $totalBelumInput++;
                $auditRecords[] = [
                    'status_audit' => 'NOT_FOUND_IN_DB',
                    'nik' => $nik,
                    'nama' => $entry->nama,
                    'dept' => $entry->department,
                    'section' => $entry->section,
                    'tanggal' => $tanggal,
                    'tanggal_formatted' => Carbon::parse($tanggal)->isoFormat('D MMMM YYYY'),
                    'mangkir_ke' => $entry->mangkir_ke,
                    'code' => $code,
                    'employee_id' => null,
                    'sp_id' => null,
                    'nomor_sp' => null,
                    'current_status' => null,
                    'creator_name' => null,
                    'created_at' => null,
                ];
            }
        }

        return [
            'period_key' => $periodKey,
            'period_formatted' => $periodFormatted,
            'target_codes' => $targetCodes,
            'total_excel_mangkir' => count($dbRecords),
            'total_sudah_input' => $totalSudahInput,
            'total_belum_input' => $totalBelumInput,
            'total_karyawan_mangkir' => count($allNiks),
            'audit_records' => $auditRecords,
            'filename' => $dbRecords->first()->filename ?? '-',
            'created_at' => $dbRecords->first()->created_at ? $dbRecords->first()->created_at->format('d/m/Y H:i') : '-',
        ];
    }

    /**
     * Parse period string from header (e.g. "Periode: JUNI 2026")
     */
    private function parsePeriodString(string $periodString): array
    {
        $defaultMonth = (int)date('m');
        $defaultYear = (int)date('Y');

        if (empty($periodString)) {
            return ['month' => $defaultMonth, 'year' => $defaultYear];
        }

        $monthMap = [
            'JANUARI' => 1, 'JANUARY' => 1, 'JAN' => 1,
            'FEBRUARI' => 2, 'FEBRUARY' => 2, 'FEB' => 2,
            'MARET' => 3, 'MARCH' => 3, 'MAR' => 3,
            'APRIL' => 4, 'APR' => 4,
            'MEI' => 5, 'MAY' => 5,
            'JUNI' => 6, 'JUNE' => 6, 'JUN' => 6,
            'JULI' => 7, 'JULY' => 7, 'JUL' => 7,
            'AGUSTUS' => 8, 'AUGUST' => 8, 'AGU' => 8, 'AUG' => 8,
            'SEPTEMBER' => 9, 'SEP' => 9,
            'OKTOBER' => 10, 'OCTOBER' => 10, 'OKT' => 10, 'OCT' => 10,
            'NOVEMBER' => 11, 'NOV' => 11,
            'DESEMBER' => 12, 'DECEMBER' => 12, 'DES' => 12, 'DEC' => 12,
        ];

        $upper = strtoupper($periodString);
        $month = $defaultMonth;
        $year = $defaultYear;

        if (preg_match('/\b(20\d{2})\b/', $upper, $mYear)) {
            $year = (int)$mYear[1];
        }

        foreach ($monthMap as $mName => $mNum) {
            if (mb_stripos($upper, $mName) !== false) {
                $month = $mNum;
                break;
            }
        }

        return ['month' => $month, 'year' => $year];
    }
}
