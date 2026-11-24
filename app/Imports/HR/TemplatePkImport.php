<?php

namespace App\Imports\HR;

use PhpOffice\PhpSpreadsheet\RichText\RichText;

class TemplatePkImport
{
    public $parsed;

    public function __construct()
    {
        $this->parsed = [
            'periode' => null,
            'details' => [],
        ];
    }

    public function parse(string $path): void
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);

        $periode = null;
        $title = strtoupper($this->cellText($sheet, 'A', 1) . ' ' . $this->cellText($sheet, 'B', 3) . ' ' . $this->cellText($sheet, 'B', 4));
        if (preg_match('/(20\d{2})/', $title, $m)) {
            $periode = $m[1];
        }

        $details = [];
        $section = null;
        $category = null;
        $levelGroup = null;

        for ($r = 1; $r <= $sheet->getHighestRow(); $r++) {
            $no = $this->cellText($sheet, 'A', $r);
            $area = $this->cellText($sheet, 'B', $r);
            $competency = $this->cellText($sheet, 'C', $r);

            if ($no === '' && $area === '' && $competency === '') {
                continue;
            }

            if ($no === 'NO') {
                continue;
            }

            if ($area === '' && $competency === '' && $no !== '') {
                $sectionInfo = $this->sectionInfo($no);
                if ($sectionInfo !== null) {
                    $section = $no;
                    $category = $sectionInfo['category'];
                    $levelGroup = $sectionInfo['level_group'];
                }
                continue;
            }

            if (!preg_match('/^\d+(\.\d+)?$/', $no)) {
                continue;
            }

            if ($section === null) {
                continue;
            }

            $details[] = [
                'section' => $section,
                'category' => $category,
                'level_group' => $levelGroup,
                'no_urut' => $no,
                'area' => $area,
                'competency_behavior' => $competency,
                'key_behavior' => $this->cellText($sheet, 'D', $r),
                'kriteria_1' => $this->cellText($sheet, 'E', $r),
                'kriteria_2' => $this->cellText($sheet, 'F', $r),
                'kriteria_3' => $this->cellText($sheet, 'G', $r),
                'kriteria_4' => $this->cellText($sheet, 'H', $r),
                'kriteria_5' => $this->cellText($sheet, 'I', $r),
            ];
        }

        $this->parsed = [
            'periode' => $periode,
            'details' => $details,
        ];
    }

    private function sectionInfo(string $title): ?array
    {
        $t = strtoupper($title);
        if (str_contains($t, 'CORE VALUE')) {
            return ['category' => 'WINGS CORE VALUE', 'level_group' => 'all'];
        }
        if (str_contains($t, 'LEADERSHIP')) {
            if (str_contains($t, '2-3') || str_contains($t, '2 - 3')) {
                return ['category' => 'LEADERSHIP COMPETENCY', 'level_group' => '2-3'];
            }
            if (str_contains($t, '4-5') || str_contains($t, '4 - 5')) {
                return ['category' => 'LEADERSHIP COMPETENCY', 'level_group' => '4-5'];
            }
            if (str_contains($t, '6-7') || str_contains($t, '6 - 7')) {
                return ['category' => 'LEADERSHIP COMPETENCY', 'level_group' => '6-7'];
            }
            return ['category' => 'LEADERSHIP COMPETENCY', 'level_group' => 'all'];
        }
        if (str_contains($t, 'SIKAP KERJA')) {
            return ['category' => 'SIKAP KERJA', 'level_group' => '2-3'];
        }
        if (str_contains($t, 'BEHAVIOR')) {
            return ['category' => 'BEHAVIOR', 'level_group' => '2-3'];
        }
        if (str_contains($t, 'COMPLIANCE')) {
            return ['category' => 'COMPLIANCE', 'level_group' => 'all'];
        }
        return null;
    }

    private function cellText($sheet, string $col, int $row = 1): string
    {
        $v = $sheet->getCell($col . $row)->getValue();
        if ($v instanceof RichText) {
            $v = $v->getPlainText();
        }
        if (is_object($v) || $v === null) {
            return '';
        }
        return trim(preg_replace('/[ \t\r]+/', ' ', (string) $v));
    }
}