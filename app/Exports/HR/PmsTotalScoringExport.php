<?php

namespace App\Exports\HR;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PmsTotalScoringExport implements FromArray, WithCharts, WithColumnWidths, WithEvents, WithTitle
{
    private const LAST_COL = 'Q';
    private const GROUP_ROW = 3;
    private const HEADER_ROW = 4;
    private const DATA_START_ROW = 5;

    private const HEADERS = [
        'NO', 'PERIODE', 'NIK', 'NAMA', 'DEPT', 'LEVEL', 'BAGIAN', 'NAMA POSISI',
        'PUNYA BAWAHAN', 'FORM IPP', 'FORM PK', 'IPP SCORE', 'PK SCORE',
        'HASIL FORMULASI', "PA SCALE\nORIGINAL",
        "PA SCALE\nNORMALIZE", "PA SCALE\nCALIBRATED",
    ];

    private const GRADE_STYLE = [
        'A' => ['bg' => 'E8F5E9', 'font' => '1B5E20'],
        'B' => ['bg' => 'E3F2FD', 'font' => '0D47A1'],
        'C' => ['bg' => 'FFF3E0', 'font' => 'E65100'],
        'D' => ['bg' => 'FDECEA', 'font' => 'A80000'],
        'E' => ['bg' => 'ECEFF1', 'font' => '455A64'],
    ];

    private const SUMMARY_ROW_FILL = [
        0 => 'D9E1F2',
        1 => 'E2EFDA',
        2 => 'FFF3CD',
        3 => 'FFF2CC',
        4 => 'FCE4D6',
    ];

    /** @var array<int, array> */
    private array $rows;

    /** @var array<string, mixed>|null */
    private ?array $summary;

    private string $periodeLabel;

    /** @var array<string, string> */
    private array $filters;

    private int $dataRowCount;
    private int $dataEndRow;
    private ?int $summaryTitleRow = null;
    private ?int $summaryHeaderRow = null;
    private ?int $summaryStartRow = null;
    private ?int $summaryEndRow = null;

    /**
     * @param  array<int, array>  $rows
     * @param  array<string, mixed>|null  $summary
     * @param  array<string, string>  $filters
     */
    public function __construct(array $rows, ?array $summary, string $periodeLabel, array $filters = [])
    {
        $this->rows = array_values($rows);
        $this->summary = $summary;
        $this->periodeLabel = $periodeLabel;
        $this->filters = $filters;

        $this->dataRowCount = count($this->rows);
        $this->dataEndRow = self::DATA_START_ROW + max($this->dataRowCount, 1) - 1;

        if ($this->summary !== null) {
            $this->summaryTitleRow = $this->dataEndRow + 2;
            $this->summaryHeaderRow = $this->dataEndRow + 3;
            $this->summaryStartRow = $this->dataEndRow + 4;
            $this->summaryEndRow = $this->summaryHeaderRow + 5;
        }
    }

    public function array(): array
    {
        $grid = [];

        $grid[] = ['HASIL TOTAL SCORING — PERIODE ' . strtoupper($this->periodeLabel)];
        $grid[] = [$this->infoLine()];

        $groupRow = array_fill(0, 17, null);
        $groupRow[11] = 'END YEAR RESULT';
        $groupRow[13] = 'END YEAR FINAL RATING';
        $grid[] = $groupRow;

        $grid[] = self::HEADERS;

        if (!$this->rows) {
            $emptyRow = array_fill(0, 17, null);
            $emptyRow[0] = 'Tidak ada data untuk filter ini.';
            $grid[] = $emptyRow;
        } else {
            foreach ($this->rows as $i => $r) {
                $bagian = trim((string) ($r['bagian'] ?? ''));
                $bagian = $bagian !== '' ? $bagian : '-';

                $grid[] = [
                    $i + 1,
                    (string) ($r['periode'] ?? ''),
                    (string) ($r['nik'] ?? ''),
                    (string) ($r['nama'] ?? ''),
                    (string) ($r['dept_label'] ?? $r['departemen'] ?? ''),
                    (string) ($r['level'] ?? ''),
                    (string) $bagian,
                    (string) ($r['jabatan'] ?? ''),
                    $r['punya_bawahan'] === null ? '-' : (int) $r['punya_bawahan'],
                    !empty($r['form_ipp_url']) ? 'IPP' : '-',
                    !empty($r['form_pk_url']) ? 'PK' : '-',
                    $r['ipp_score'] === null ? '-' : (float) $r['ipp_score'],
                    $r['pk_score'] === null ? '-' : (float) $r['pk_score'],
                    $r['final_score'] === null ? '-' : (float) $r['final_score'],
                    !empty($r['pa_scale']) ? $r['pa_scale'] : '-',
                    !empty($r['pa_scale_rank']) ? $r['pa_scale_rank'] : '-',
                    !empty($r['pa_scale_final']) ? $r['pa_scale_final'] : '-',
                ];
            }
        }

        if ($this->summary !== null) {
            $s = $this->summary;

            $grid[] = array_fill(0, 17, null);

            $titleRow = array_fill(0, 17, null);
            $titleRow[1] = 'RINGKASAN PA SCALE GABUNGAN (LEVEL 2-3 & 4-7) — PERIODE ' . strtoupper($this->periodeLabel)
                . ' (' . (int) ($s['num_people'] ?? 0) . ' orang)';
            $grid[] = $titleRow;

            $headRow = array_fill(0, 17, null);
            foreach (['Keterangan', 'E', 'D', 'C', 'B', 'A'] as $i => $label) {
                $headRow[1 + $i] = $label;
            }
            $grid[] = $headRow;

            $grid[] = $this->summaryRow('PA SCALE Original', $s['converted'] ?? []);
            $grid[] = $this->summaryRow('PA SCALE Normalize', $s['rank'] ?? []);
            $grid[] = $this->summaryRow('PA SCALE Calibrated', $s['final'] ?? []);
            $grid[] = $this->summaryRow('Kuota Normalize', $s['rank_quota'] ?? []);
            $grid[] = $this->summaryRow('Persen Kuota', $s['percent'] ?? [], true);
        }

        return $grid;
    }

    /**
     * @param  array<string, mixed>|null  $values
     * @return array<int, mixed>
     */
    private function summaryRow(string $label, ?array $values, bool $asPercent = false): array
    {
        $row = array_fill(0, 17, null);
        $row[1] = $label;

        foreach (['E', 'D', 'C', 'B', 'A'] as $i => $grade) {
            $value = $values[$grade] ?? null;
            $row[2 + $i] = ($value !== null && $asPercent) ? ((float) $value / 100) : $value;
        }

        return $row;
    }

    private function infoLine(): string
    {
        $parts = ['Dicetak: ' . now()->format('d/m/Y H:i')];

        if (!empty($this->filters['group'])) {
            $groupLabel = $this->filters['group'] === '4-7'
                ? 'Level 4-7 (4-5 & 6-7)'
                : 'Level ' . $this->filters['group'];
            $parts[] = 'Group: ' . $groupLabel;
        }
        if (!empty($this->filters['departemen'])) {
            $label = ($this->filters['dept_type'] ?? 'dept') === 'sub' ? 'Bagian' : 'Departemen';
            $parts[] = $label . ': ' . $this->filters['departemen'];
        }
        if (!empty($this->filters['search'])) {
            $parts[] = 'Cari: "' . $this->filters['search'] . '"';
        }
        if (!empty($this->filters['pa_scale_type'])) {
            $parts[] = 'PA Scale: ' . ucfirst((string) $this->filters['pa_scale_type']);
        }
        if (!empty($this->filters['pa_scale_grade'])) {
            $parts[] = 'Grade: ' . $this->filters['pa_scale_grade'];
        }

        return implode('   •   ', $parts);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 9,
            'C' => 15,
            'D' => 28,
            'E' => 26,
            'F' => 7,
            'G' => 26,
            'H' => 30,
            'I' => 11,
            'J' => 9,
            'K' => 9,
            'L' => 10,
            'M' => 10,
            'N' => 12,
            'O' => 11,
            'P' => 13,
            'Q' => 11,
        ];
    }

    public function title(): string
    {
        return 'Total Scoring ' . $this->periodeLabel;
    }

    /**
     * Grafik kurva PA Scale (4 seri, mengikuti nilai sel blok ringkasan).
     *
     * @return Chart[]
     */
    public function charts()
    {
        if ($this->summary === null || $this->summaryHeaderRow === null || $this->summaryStartRow === null) {
            return [];
        }

        $sheetRef = "'" . str_replace("'", "''", $this->title()) . "'";
        $headRow = $this->summaryHeaderRow;
        $s0 = $this->summaryStartRow;

        $categories = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            $sheetRef . '!$C$' . $headRow . ':$G$' . $headRow,
            null,
            5
        );

        $definitions = [
            ['PA SCALE Original', $s0, '4472C4'],
            ['PA SCALE Normalize', $s0 + 1, '7030A0'],
            ['PA SCALE Calibrated', $s0 + 2, 'BF9000'],
            ['Kuota Final', $s0 + 3, 'C00000'],
        ];

        $series = [];
        foreach ($definitions as $definition) {
            [$label, $rowNo, $rgb] = $definition;

            $values = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $sheetRef . '!$C$' . $rowNo . ':$G$' . $rowNo,
                null,
                5
            );
            $values->setPointMarker('circle');
            $values->setPointSize(5);
            $values->setLineColorProperties($rgb, null, ChartColor::EXCEL_COLOR_TYPE_RGB);
            $values->getMarkerFillColor()->setColorPropertiesArray([
                'type' => ChartColor::EXCEL_COLOR_TYPE_RGB,
                'value' => $rgb,
                'alpha' => null,
            ]);
            $values->getMarkerBorderColor()->setColorPropertiesArray([
                'type' => ChartColor::EXCEL_COLOR_TYPE_RGB,
                'value' => 'FFFFFF',
                'alpha' => null,
            ]);

            $series[] = new DataSeries(
                DataSeries::TYPE_LINECHART,
                DataSeries::GROUPING_STANDARD,
                [0],
                [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, [$label])],
                [$categories],
                [$values],
                DataSeries::DIRECTION_COL,
                true
            );
        }

        $chart = new Chart(
            'pa_scale_curve',
            new Title('PA Scale — Periode ' . $this->periodeLabel),
            new Legend(Legend::POSITION_TOPRIGHT, null, false),
            new PlotArea(null, $series)
        );
        $chart->setXAxisLabel(new Title('PA Scale'));
        $chart->setYAxisLabel(new Title('Jumlah Orang'));
        $chart->getChartAxisY()->setAxisOptionsProperties('nextTo', null, null, null, null, null, '0');
        $chart->setTopLeftPosition('I' . ($s0 - 1));
        $chart->setBottomRightPosition('Q' . ($s0 + 13));

        return [$chart];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->styleSheet($sheet);
            },
        ];
    }

    private function styleSheet(Worksheet $sheet): void
    {
        $last = self::LAST_COL;
        $groupRow = self::GROUP_ROW;
        $headerRow = self::HEADER_ROW;
        $ds = self::DATA_START_ROW;
        $de = $this->dataEndRow;

        $sheet->mergeCells("A1:{$last}1");
        $sheet->mergeCells("A2:{$last}2");
        $sheet->mergeCells("A{$groupRow}:K{$groupRow}");
        $sheet->mergeCells("L{$groupRow}:M{$groupRow}");
        $sheet->mergeCells("N{$groupRow}:{$last}{$groupRow}");

        $sheet->getStyle("A1:{$last}1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'A80000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $sheet->getStyle("A2:{$last}2")->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '757575']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(16);

        $sheet->getStyle("A{$groupRow}:K{$groupRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '000000']],
        ]);
        $sheet->getStyle("L{$groupRow}:M{$groupRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("N{$groupRow}:{$last}{$groupRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'A80000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($groupRow)->setRowHeight(20);

        $sheet->getStyle("A{$headerRow}:{$last}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '000000']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getStyle("A{$headerRow}:{$last}{$headerRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("L{$headerRow}:M{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFC000']],
        ]);
        $sheet->getStyle("N{$headerRow}:{$last}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F8CECC']],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(34);

        $sheet->getStyle("A{$ds}:{$last}{$de}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("A{$ds}:{$last}{$de}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$ds}:E{$de}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("G{$ds}:H{$de}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("L{$ds}:N{$de}")->getNumberFormat()->setFormatCode('0.00');

        if (!$this->rows) {
            $sheet->mergeCells("A{$ds}:{$last}{$ds}");
            $sheet->getStyle("A{$ds}:{$last}{$ds}")->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => '9E9E9E']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        for ($i = 0; $i < $this->dataRowCount; $i++) {
            $rowNo = $ds + $i;
            $r = $this->rows[$i];

            if (!empty($r['form_ipp_url'])) {
                $sheet->getCell("J{$rowNo}")->getHyperlink()
                    ->setUrl($r['form_ipp_url'])->setTooltip('Buka form IPP');
                $sheet->getStyle("J{$rowNo}")->getFont()
                    ->setColor(new Color('0563C1'))->setUnderline(true);
            }
            if (!empty($r['form_pk_url'])) {
                $sheet->getCell("K{$rowNo}")->getHyperlink()
                    ->setUrl($r['form_pk_url'])->setTooltip('Buka form PK');
                $sheet->getStyle("K{$rowNo}")->getFont()
                    ->setColor(new Color('0563C1'))->setUnderline(true);
            }

            foreach (['O' => 'pa_scale', 'P' => 'pa_scale_rank', 'Q' => 'pa_scale_final'] as $col => $key) {
                $grade = strtoupper(trim((string) ($r[$key] ?? '')));
                if (isset(self::GRADE_STYLE[$grade])) {
                    $sheet->getStyle("{$col}{$rowNo}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => self::GRADE_STYLE[$grade]['font']]],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => self::GRADE_STYLE[$grade]['bg']],
                        ],
                    ]);
                }
            }
        }

        $sheet->freezePane('A' . $ds);
        $sheet->setAutoFilter("A{$headerRow}:{$last}{$de}");
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $headerRow);
        $this->styleSummary($sheet);
    }

    private function styleSummary(Worksheet $sheet): void
    {
        if ($this->summaryTitleRow === null || $this->summaryHeaderRow === null
            || $this->summaryStartRow === null || $this->summaryEndRow === null) {
            return;
        }

        $titleRow = $this->summaryTitleRow;
        $headRow = $this->summaryHeaderRow;
        $s0 = $this->summaryStartRow;
        $s1 = $this->summaryEndRow;

        $sheet->mergeCells("B{$titleRow}:G{$titleRow}");
        $sheet->getStyle("B{$titleRow}:G{$titleRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D9D9D9']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle("B{$headRow}:G{$headRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F2F2F2']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFBFBF']]],
        ]);

        $sheet->getStyle("B{$s0}:G{$s1}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFBFBF']]],
        ]);
        $sheet->getStyle("B{$s0}:B{$s1}")->getFont()->setBold(true);
        $sheet->getStyle("B{$s0}:B{$s1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        foreach (self::SUMMARY_ROW_FILL as $offset => $rgb) {
            $rowNo = $s0 + $offset;
            $sheet->getStyle("B{$rowNo}:G{$rowNo}")->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($rgb);
        }

        $kurvaRow = $s0 + 3;
        $persenRow = $s0 + 4;
        $sheet->getStyle("C{$kurvaRow}:G{$kurvaRow}")->getNumberFormat()->setFormatCode('0');
        $sheet->getStyle("C{$persenRow}:G{$persenRow}")->getNumberFormat()->setFormatCode('0%');
    }
}
