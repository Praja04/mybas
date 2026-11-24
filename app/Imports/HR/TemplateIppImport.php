<?php

namespace App\Imports\HR;

use App\Support\IppRangeScale;
use Maatwebsite\Excel\Concerns\ToArray;

class TemplateIppImport implements ToArray
{
    public $parsed;

    public function __construct()
    {
        $this->parsed = [
            'periode' => null,
            'nama' => null,
            'nik_peserta' => [],
            'details' => [],
        ];
    }

    public function array(array $rows)
    {
        $normalized = [];
        foreach ($rows as $row) {
            $values = [];
            foreach (array_pad(array_slice($row, 0, 9), 9, null) as $cell) {
                if ($cell === null) {
                    $values[] = null;
                    continue;
                }
                if ($cell instanceof \DateTimeInterface) {
                    $values[] = $cell->format('Y-m-d');
                    continue;
                }
                $values[] = trim((string) $cell);
            }
            $values = array_pad($values, 9, null);
            $normalized[] = $values;
        }

        $meta = $this->parsed;
        $details = [];

        $headerIdx = null;
        $cols = null;

        foreach ($normalized as $idx => $values) {
            if ($cols !== null) {
                break;
            }
            $v0 = strtolower(trim((string) ($values[0] ?? '')));
            if (preg_match('/^no\.?$/', $v0)) {
                $cols = $this->mapHeaders($values);
                $headerIdx = $idx;
            }
        }

        // Deteksi baris PERIODE PENILAIAN
        foreach ($normalized as $idx => $values) {
            $v0 = strtoupper(trim((string) ($values[0] ?? '')));
            if (str_contains($v0, 'PERIODE')) {
                $meta['periode'] = $values[1] !== null ? trim($values[1]) : null;
                break;
            }
        }

        foreach ($normalized as $idx => $values) {
            $v0 = strtoupper(trim((string) ($values[0] ?? '')));
            if (str_contains($v0, 'NAMA')) {
                $label = $values[1] !== null ? trim($values[1]) : '';
                $meta['nama'] = $label !== '' ? $label : null;
                $meta['nik_peserta'] = $this->extractNiks($label);
                break;
            }
        }

        if ($cols === null || $headerIdx === null) {
            $this->parsed = $meta;
            return;
        }

        for ($i = $headerIdx + 1; $i < count($normalized); $i++) {
            $values = $normalized[$i];

            $no = $this->val($cols, $values, 'no');
            $kpi = $this->val($cols, $values, 'kpi');

            if (($no === null || $no === '') && ($kpi === null || $kpi === '')) {
                continue;
            }

            $details[] = [
                'no_urut' => (int) $no,
                'objective' => $this->val($cols, $values, 'objective'),
                'kpa' => $this->val($cols, $values, 'kpa'),
                'kpi' => $kpi,
                'classification' => $this->val($cols, $values, 'classification'),
                'uom' => $this->val($cols, $values, 'uom'),
                'weight' => $this->numOrNull($this->val($cols, $values, 'weight')),
                'target' => $this->val($cols, $values, 'target'),
                'due_date' => $this->val($cols, $values, 'due'),
            ];
        }

        $meta['details'] = IppRangeScale::hydrateDetails($details);

        $this->parsed = $meta;
    }

    private function extractNiks(string $label): array
    {
        $out = [];
        if ($label === '') {
            return $out;
        }

        foreach (explode(',', $label) as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            if (preg_match('/([0-9]{6}-[0-9]{3,6}|[0-9]{7,10})\s*$/', $segment, $match) !== 1) {
                continue;
            }

            $nik = trim($match[1]);
            if ($nik !== '' && !in_array($nik, $out, true)) {
                $out[] = $nik;
            }
        }

        return $out;
    }

    private function val(array $cols, array $values, string $key)
    {
        return array_key_exists($key, $cols) && array_key_exists($cols[$key], $values)
            ? $values[$cols[$key]]
            : null;
    }

    private function mapHeaders(array $values): array
    {
        $map = [];
        foreach ($values as $idx => $name) {
            if ($name === null || $name === '') {
                continue;
            }
            $key = strtolower(trim((string) $name));
            if ($key === 'no' || $key === 'no.' || $key === 'no ') {
                $map['no'] = $idx;
            } elseif (str_contains($key, 'objective')) {
                $map['objective'] = $idx;
            } elseif (str_contains($key, 'kpa') || str_contains($key, 'key performance area')) {
                $map['kpa'] = $idx;
            } elseif (str_contains($key, 'kpi') || str_contains($key, 'key performance indicator')) {
                $map['kpi'] = $idx;
            } elseif (str_contains($key, 'classification')) {
                $map['classification'] = $idx;
            } elseif (str_contains($key, 'unit of measurement')) {
                $map['uom'] = $idx;
            } elseif ($key === 'weight') {
                $map['weight'] = $idx;
            } elseif ($key === 'target') {
                $map['target'] = $idx;
            } elseif (str_contains($key, 'due')) {
                $map['due'] = $idx;
            }
        }

        // Fallback template lama (tanpa kolom Weight):
        // No | Objective | KPA | KPI | Classification | Unit Of Measurement | Target | Due date
        if (!isset($map['weight'])) {
            if (!isset($map['target']) && isset($map['uom'])) {
                $map['target'] = $map['uom'] + 1;
            }
            if (!isset($map['due']) && isset($map['target'])) {
                $map['due'] = $map['target'] + 1;
            }
        }

        return $map;
    }

    protected function numOrNull($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = trim((string) $value);
        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }
        if (!is_numeric($value)) {
            return null;
        }
        return (float) $value;
    }

    protected function num($value): float
    {
        return $this->numOrNull($value) ?? 0;
    }
}