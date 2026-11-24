<?php

namespace App\Imports\HR;

use PhpOffice\PhpSpreadsheet\RichText\RichText;

/**
 * Importer template rubrik IPP Level 2-3 (satu template untuk semua peserta).
 * Kolom: A=NO, B=AREA, C=COMPETENCY BEHAVIOR, D=KEY BEHAVIOR, E..I=SCORING CRITERIA 1..5.
 */
class TemplateIppForemanImport
{
    public $parsed;

    public function __construct()
    {
        $this->parsed = [
            'details' => [],
        ];
    }

    public function parse(string $path): void
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);

        $details = [];
        $section = null;

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
                $section = $no;
                continue;
            }

            if (!preg_match('/^\d+(\.\d+)?$/', $no)) {
                continue;
            }

            $details[] = [
                'section' => $section,
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
            'details' => $details,
        ];
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
