<?php

namespace App\Support;

class HrEmployeeNormalizer
{
    public static function normalizeDate($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $value)) {
            $parts = explode('-', $value);
            return sprintf('%04d-%02d-%02d', $parts[0], $parts[1], $parts[2]);
        }

        if (preg_match('/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$/', $value)) {
            $separator = strpos($value, '/') !== false ? '/' : '-';
            $parts = explode($separator, $value);
            if (strlen($parts[2]) === 4) {
                $a = (int) $parts[0];
                $b = (int) $parts[1];
                $year = (int) $parts[2];

                // Excel data menggunakan format m/d/Y (US), coba dulu
                if (checkdate($a, $b, $year)) {
                    return sprintf('%04d-%02d-%02d', $year, $a, $b);
                }
                // Fallback ke d/m/Y (Eropa/Indonesia)
                if (checkdate($b, $a, $year)) {
                    return sprintf('%04d-%02d-%02d', $year, $b, $a);
                }
            }
        }

        if (preg_match('/^\d{1,2}-([A-Za-z]{3,9})-\d{4}$/', $value, $m)) {
            foreach (['d-M-Y', 'd-F-Y'] as $fmt) {
                $date = \DateTime::createFromFormat($fmt, $value);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
        }

        if (is_numeric($value)) {
            $serial = (float) $value;
            if ($serial > 0) {
                $unix = ($serial - 25569) * 86400;
                return gmdate('Y-m-d', (int) $unix);
            }
        }

        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    public static function removePasPrefix($value)
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        return preg_replace('/^PAS\s+/i', '', $value);
    }

    private const DEPT_SUBDEPT_MAPPING = [
        // ===== Key 2-segmen: 'dept|subdept' (umum, tanpa section) =====
        'engineering|engineering project & workshop'                  => ['PAS Engineering & Health Safety Environment', 'PAS Engineering Civil & Workshop'],
        'engineering|engineering utility'                             => ['PAS Engineering & Health Safety Environment', 'PAS Engineering Utility'],
        'engineering|hse'                                             => ['PAS Engineering & Health Safety Environment', 'PAS Health Safety Environment'],
        'engineering|r&i'                                             => ['PAS Engineering & Health Safety Environment', 'PAS Health Safety Environment'],
        'engineering|engineering mechanical electrical automation'    => ['PAS Engineering & Health Safety Environment', 'PAS Engineering Project Mechanical Electrical Automation'],
        'exspedisi|exspedisi'                                         => ['PAS Factory', 'PAS Expedition'],
        'factory manager|'                                            => ['PAS Factory', 'PAS Information Technology'],
        'finance & accounting|finance & accounting'                   => ['PAS Finance, Accounting & Tax', 'PAS Finance'],
        'hrd & ga|hrga'                                               => ['PAS Human Resources & General Affairs', 'PAS General Affairs'],
        'hrd & ga|hr ga'                                              => ['PAS Human Resources & General Affairs', 'PAS General Affairs'],
        'hrd & ga|hr learning & development'                          => ['PAS Human Resources & General Affairs', 'PAS HR Learning & Development'],
        'hrd & ga|hr operation'                                       => ['PAS Human Resources & General Affairs', 'PAS HR Operations'],
        'ir & er|hr ir'                                               => ['PAS Industrial & External Relation', 'PAS Industrial & External Relation'],
        'ppic|ppic'                                                   => ['PAS Factory', 'PAS Production Planning Inventory Control'],
        'produksi noodle 1|produksi noodle 1'                         => ['PAS Factory', 'PAS Production Noodle 1'],
        'produksi noodle 1|engineering produksi noodle 1'             => ['PAS Factory', 'PAS Production Noodle 1'],
        'produksi noodle 2|produksi noodle 2'                         => ['PAS Factory', 'PAS Production Noodle 2'],
        'produksi noodle 2|engineering produksi noodle 2'             => ['PAS Factory', 'PAS Production Noodle 2'],
        'produksi seasoning 1|produksi seasoning 1'                   => ['PAS Factory', 'PAS Production Seasoning 1'],
        'produksi seasoning 1|engineering produksi seasoning 1'       => ['PAS Factory', 'PAS Production Seasoning 1'],
        'produksi seasoning 2|produksi seasoning 2'                   => ['PAS Factory', 'PAS Production Seasoning 2'],
        'produksi seasoning 2|engineering produksi seasoning 2'       => ['PAS Factory', 'PAS Production Seasoning 2'],
        'produksi seasoning 3|produksi seasoning 3'                   => ['PAS Factory', 'PAS Production Seasoning 3'],
        'produksi seasoning 3|engineering produksi seasoning 3'       => ['PAS Factory', 'PAS Production Seasoning 3'],
        'purchasing raw material|purchasing improvement analyst'      => ['PAS Factory', 'PAS Purchasing Raw Material'],
        'purchasing raw material|purchasing operaional & corporate raw material' => ['PAS Factory', 'PAS Purchasing Raw Material'],
        'purchasing raw material|purchasing raw material'             => ['PAS Factory', 'PAS Purchasing Raw Material'],
        'purchasing spare part|purchasing spare part'                 => ['PAS Purchasing Sparepart', 'PAS Purchasing Sparepart Operations & Project'],
        'quality control|qc seasoning'                                => ['PAS Factory', 'PAS Quality Control'],
        'quality control|qc noodle'                                   => ['PAS Factory', 'PAS Quality Control'],
        'quality control|qc raw material'                             => ['PAS Factory', 'PAS Quality Control'],
        'rnd & lab|qa lab'                                            => ['PAS Research Development & Quality Assurance', 'PAS Quality Assurance Laboratorium'],
        'rnd & lab|r&d development'                                   => ['PAS Research Development & Quality Assurance', 'PAS R&D Premix Seasoning'],
        'warehouse 01|warehouse 01'                                  => ['PAS Factory', 'PAS Warehouse'],
        'warehouse 02|warehouse 02'                                  => ['PAS Factory', 'PAS Warehouse'],

        // ===== Key 3-segmen: 'dept|subdept|section' (spesifik pakai section) =====
        // Finance & Accounting + Finance & Accounting + section
        'finance & accounting|finance & accounting|finance'                  => ['PAS Finance, Accounting & Tax', 'PAS Finance'],
        'finance & accounting|finance & accounting|finance tax'              => ['PAS Finance, Accounting & Tax', 'PAS Tax'],
        'finance & accounting|finance & accounting|accounting'               => ['PAS Finance, Accounting & Tax', 'PAS Accounting'],
        'finance & accounting|finance & accounting|finance cost'             => ['PAS Finance, Accounting & Tax', 'PAS Accounting'],
        'finance & accounting|finance & accounting|finance cashier'          => ['PAS Finance, Accounting & Tax', 'PAS Finance'],
        'finance & accounting|finance & accounting|finance account payble'   => ['PAS Finance, Accounting & Tax', 'PAS Accounting'],
        // Purchasing Raw Material + subdept kosong + section
        'purchasing raw material||purchasing operasional raw material'      => ['PAS Factory', 'PAS Purchasing Raw Material'],
        'purchasing raw material||purchasing merchendise & co-product'      => ['PAS Factory', 'PAS Purchasing Raw Material'],
    ];

    public static function normalizeDepartmenAndSubDepartmen($departmen, $subDepartmen, $section = null): array
    {
        $d   = trim((string) ($departmen ?? ''));
        $s   = trim((string) ($subDepartmen ?? ''));
        $sec = trim((string) ($section ?? ''));

        if ($d === '' && $s === '' && $sec === '') {
            return [null, null];
        }

        $dLow   = strtolower($d);
        $sLow   = strtolower($s);
        $secLow = strtolower($sec);

        $key3 = $dLow . '|' . $sLow . '|' . $secLow;
        $key2 = $dLow . '|' . $sLow;

        $mapped = self::DEPT_SUBDEPT_MAPPING[$key3]
            ?? self::DEPT_SUBDEPT_MAPPING[$key2]
            ?? null;

        if ($mapped !== null) {
            return [
                self::removePasPrefix($mapped[0]),
                self::removePasPrefix($mapped[1]),
            ];
        }

        return [
            $d === '' ? null : self::removePasPrefix($d),
            $s === '' ? null : self::removePasPrefix($s),
        ];
    }

    /**
     * Mapping Section -> Sub Departmen.
     * Lookup otomatis saat import: jika Section row match key,
     * Sub Departmen di-isi dengan value. Jika tidak match, Sub Departmen = null.
     */
    private const SECTION_TO_SUB_DEPT = [
        'Direksi'                                      => 'Direksi',
        'Engineering Automation'                       => 'Engineering Otomotif & Utility',
        'Engineering Electrical & Kalibrasi'           => 'Engineering Project & Electrical',
        'Engineering Maintenance & Improvement'        => 'Engineering Otomotif & Utility',
        'Engineering'                                  => 'Engineering',
        'Engineering Project & Drawing'                => 'Engineering Project & Electrical',
        'Engineering Produksi Retail'                  => 'Engineering Produksi',
        'Engineering Produksi Proses'                  => 'Engineering Produksi',
        'Engineering Utility & Boiler'                 => 'Engineering Otomotif & Utility',
        'Engineering Project & Electrical'             => 'Engineering Project & Electrical',
        'ITE'                                          => 'ITE',
        'Engineering WWTP'                             => 'Engineering Otomotif & Utility',
        'Accounting'                                   => 'Finance & Accounting',
        'Finance & Accounting'                         => 'Finance & Accounting',
        'Finance Kasir'                                => 'Finance & Accounting',
        'Accounting Payable'                           => 'Finance & Accounting',
        'Finance Cost'                                 => 'Finance & Accounting',
        'Finance Tax'                                  => 'Finance & Accounting',
        'Factory'                                      => 'Factory',
        'Health Safety Environment'                    => 'Factory',
        'Integrated Management System'                 => 'Factory',
        'IT Support'                                   => 'Factory',
        'Research & Development'                       => 'Factory',
        'General Affair'                               => 'General Affair',
        'HRD'                                          => 'HRD',
        'HRD & GA'                                     => 'HRD & GA',
        'Industrial Relation & External Relationship'  => 'Industrial Relation & External Relationship',
        'Production Planning Control'                  => 'Production Planning Control',
        'Engineering Produksi'                         => 'Engineering Produksi',
        'Produksi Palletizing'                         => 'Produksi Proses',
        'Produksi Filling Retail'                      => 'Produksi Proses',
        'Produksi Material Balance & Project'          => 'Produksi Material Balance & Project',
        'Produksi'                                     => 'Produksi',
        'Produksi Proses'                              => 'Produksi Proses',
        'Purchasing Raw Material'                      => 'Purchasing Raw Material',
        'Purchasing Sparepart'                         => 'Purchasing Sparepart',
        'Dokumen Control'                              => 'Integrated Management System',
        'Quality Control Filling'                      => 'Quality Control Produksi',
        'Quality Control Kimia'                        => 'Quality Control Produksi',
        'Quality Control Mikrobiologi'                 => 'Quality Control Produksi',
        'Quality Control'                              => 'Quality Control',
        'Quality Control Proses'                       => 'Quality Control Produksi',
        'Quality Control Premix'                       => 'Quality Control Premix',
        'Quality Control Raw & Packaging Material'     => 'Quality Control Raw & Packaging Material',
        'Warehouse Co Product'                         => 'Warehouse',
        'Warehouse Finish Good'                        => 'Warehouse',
        'Warehouse Premix Material'                    => 'Warehouse',
        'Warehouse'                                    => 'Warehouse',
        'Warehouse Raw Material'                       => 'Warehouse',
        'Warehouse Sparepart'                          => 'Warehouse',
        'Expedisi'                                     => 'Expedisi',
        'Timbangan'                                    => 'Expedisi',
    ];

    /**
     * Resolve Sub Departmen berdasarkan Section dari CSV.
     * Case-insensitive exact match. Return null jika Section tidak ada di mapping.
     */
    public static function resolveSubDeptBySection($section)
    {
        if ($section === null) {
            return null;
        }
        $section = trim((string) $section);
        if ($section === '') {
            return null;
        }
        return self::SECTION_TO_SUB_DEPT[$section] ?? null;
    }
}
