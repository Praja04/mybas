<?php

namespace App\Services;

class LokerCapacityService
{
    public function resolveMaxCapacity(string $staff): int
    {
        if ($staff === 'staff') {
            return 1;
        }

        if (in_array($staff, ['non_staff', 'mitra_kerja'])) {
            return 2;
        }

        throw new \InvalidArgumentException('Kategori karyawan tidak valid');
    }
}
