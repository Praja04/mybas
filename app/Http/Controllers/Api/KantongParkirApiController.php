<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosSecurity\KantongParkir\ParkingZone;
use App\Models\PosSecurity\KantongParkir\ParkingSlot;
use App\Models\PosSecurity\KantongParkir\ParkingAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KantongParkirApiController extends Controller
{
    /**
     * Get comprehensive data of all parking zones, including slots and their status (kosong/terisi/dll).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = ParkingZone::query();

            // Filter zone status
            if ($request->filled('status_zona')) {
                $query->where('status', $request->status_zona);
            }

            // Filter specific zone
            if ($request->filled('zone_id')) {
                $query->where('id', $request->zone_id);
            } elseif ($request->filled('kode_zona')) {
                $query->where('kode_zona', strtoupper(trim($request->kode_zona)));
            }

            // Search zone
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('kode_zona', 'like', "%{$search}%")
                      ->orWhere('nama_zona', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            // Eager load slots with active assignment
            $query->with([
                'slots' => function ($slotQuery) use ($request) {
                    // Filter slot status if provided (e.g. kosong, terisi, reserved, maintenance, non_aktif)
                    if ($request->filled('status_slot')) {
                        $slotQuery->where('status_slot', $request->status_slot);
                    }

                    // Filter vehicle type
                    if ($request->filled('jenis_kendaraan')) {
                        $slotQuery->where('jenis_kendaraan', 'like', "%{$request->jenis_kendaraan}%");
                    }

                    // Search slot
                    if ($request->filled('search_slot')) {
                        $slotQuery->where('kode_slot', 'like', "%{$request->search_slot}%");
                    }

                    $slotQuery->orderBy('kode_slot', 'asc')
                              ->with(['activeAssignment']);
                }
            ]);

            // Add aggregate counts
            $query->withCount([
                'slots as total_slot',
                'slots as slot_kosong' => function ($q) {
                    $q->where('status_slot', 'kosong');
                },
                'slots as slot_terisi' => function ($q) {
                    $q->where('status_slot', 'terisi');
                },
                'slots as slot_reserved' => function ($q) {
                    $q->where('status_slot', 'reserved');
                },
                'slots as slot_maintenance' => function ($q) {
                    $q->where('status_slot', 'maintenance');
                },
                'slots as slot_non_aktif' => function ($q) {
                    $q->where('status_slot', 'non_aktif');
                }
            ]);

            $zones = $query->orderBy('kode_zona', 'asc')->get();

            // Transform data for clean API response
            $formattedZones = $zones->map(function ($zone) {
                $slots = $zone->slots->map(function ($slot) {
                    $activeAssignment = null;
                    if ($slot->activeAssignment) {
                        $assign = $slot->activeAssignment;
                        $waktuMasuk = $assign->waktu_masuk ? Carbon::parse($assign->waktu_masuk) : null;
                        $activeAssignment = [
                            'id' => $assign->id,
                            'no_polisi' => $assign->no_polisi,
                            'jenis_kendaraan' => $assign->jenis_kendaraan,
                            'nama_driver' => $assign->nama_driver,
                            'no_hp_driver' => $assign->no_hp_driver,
                            'waktu_masuk' => $assign->waktu_masuk,
                            'durasi_parkir' => $waktuMasuk ? $waktuMasuk->diffForHumans(null, true) : null,
                            'durasi_parkir_menit' => $waktuMasuk ? (int) $waktuMasuk->diffInMinutes(now()) : null,
                            'status_assignment' => $assign->status_assignment,
                            'catatan' => $assign->catatan,
                        ];
                    }

                    return [
                        'id' => $slot->id,
                        'parking_zone_id' => $slot->parking_zone_id,
                        'kode_slot' => $slot->kode_slot,
                        'jenis_kendaraan' => $slot->jenis_kendaraan,
                        'status_slot' => $slot->status_slot, // kosong, terisi, reserved, maintenance, non_aktif
                        'keterangan' => $slot->keterangan,
                        'is_tersedia' => $slot->status_slot === 'kosong',
                        'active_vehicle' => $activeAssignment,
                        'updated_at' => $slot->updated_at ? $slot->updated_at->toIso8601String() : null
                    ];
                });

                $totalSlot = (int) $zone->total_slot;
                $slotTerisi = (int) $zone->slot_terisi;
                $slotKosong = (int) $zone->slot_kosong;
                $persentaseTerisi = $totalSlot > 0 ? round(($slotTerisi / $totalSlot) * 100, 1) : 0;

                return [
                    'id' => $zone->id,
                    'kode_zona' => $zone->kode_zona,
                    'nama_zona' => $zone->nama_zona,
                    'status_zona' => $zone->status,
                    'keterangan' => $zone->keterangan,
                    'summary' => [
                        'kapasitas_total' => $totalSlot,
                        'slot_kosong' => $slotKosong,
                        'slot_terisi' => $slotTerisi,
                        'slot_reserved' => (int) $zone->slot_reserved,
                        'slot_maintenance' => (int) $zone->slot_maintenance,
                        'slot_non_aktif' => (int) $zone->slot_non_aktif,
                        'occupancy_percentage' => $persentaseTerisi
                    ],
                    'slots' => $slots
                ];
            });

            // Global Summary Statistics
            $totalSemuaSlot = $formattedZones->sum(fn($z) => $z['summary']['kapasitas_total']);
            $totalSemuaTerisi = $formattedZones->sum(fn($z) => $z['summary']['slot_terisi']);
            $totalSemuaKosong = $formattedZones->sum(fn($z) => $z['summary']['slot_kosong']);
            $globalOccupancy = $totalSemuaSlot > 0 ? round(($totalSemuaTerisi / $totalSemuaSlot) * 100, 1) : 0;

            return response()->json([
                'success' => true,
                'message' => 'Data kantong dan slot parkir berhasil diambil',
                'summary_all' => [
                    'total_zona' => $formattedZones->count(),
                    'total_slot' => $totalSemuaSlot,
                    'total_terisi' => $totalSemuaTerisi,
                    'total_kosong' => $totalSemuaKosong,
                    'global_occupancy_percentage' => $globalOccupancy
                ],
                'data' => $formattedZones
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kantong parkir: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get flat list of parking slots with zone and status details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSlots(Request $request)
    {
        try {
            $query = ParkingSlot::with(['zone', 'activeAssignment']);

            if ($request->filled('parking_zone_id')) {
                $query->where('parking_zone_id', $request->parking_zone_id);
            }

            if ($request->filled('status_slot')) {
                $query->where('status_slot', $request->status_slot);
            }

            if ($request->filled('jenis_kendaraan')) {
                $query->where('jenis_kendaraan', 'like', "%{$request->jenis_kendaraan}%");
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('kode_slot', 'like', "%{$search}%")
                      ->orWhere('jenis_kendaraan', 'like', "%{$search}%")
                      ->orWhereHas('zone', function ($zq) use ($search) {
                          $zq->where('nama_zona', 'like', "%{$search}%")
                             ->orWhere('kode_zona', 'like', "%{$search}%");
                      })
                      ->orWhereHas('activeAssignment', function ($aq) use ($search) {
                          $aq->where('no_polisi', 'like', "%{$search}%")
                             ->orWhere('nama_driver', 'like', "%{$search}%");
                      });
                });
            }

            $slots = $query->orderBy('parking_zone_id', 'asc')
                           ->orderBy('kode_slot', 'asc')
                           ->get();

            $data = $slots->map(function ($slot) {
                $activeAssignment = null;
                if ($slot->activeAssignment) {
                    $assign = $slot->activeAssignment;
                    $waktuMasuk = $assign->waktu_masuk ? Carbon::parse($assign->waktu_masuk) : null;
                    $activeAssignment = [
                        'id' => $assign->id,
                        'no_polisi' => $assign->no_polisi,
                        'jenis_kendaraan' => $assign->jenis_kendaraan,
                        'nama_driver' => $assign->nama_driver,
                        'no_hp_driver' => $assign->no_hp_driver,
                        'waktu_masuk' => $assign->waktu_masuk,
                        'durasi_parkir' => $waktuMasuk ? $waktuMasuk->diffForHumans(null, true) : null,
                        'durasi_parkir_menit' => $waktuMasuk ? (int) $waktuMasuk->diffInMinutes(now()) : null,
                        'catatan' => $assign->catatan,
                    ];
                }

                return [
                    'id' => $slot->id,
                    'parking_zone_id' => $slot->parking_zone_id,
                    'kode_zona' => $slot->zone ? $slot->zone->kode_zona : null,
                    'nama_zona' => $slot->zone ? $slot->zone->nama_zona : null,
                    'kode_slot' => $slot->kode_slot,
                    'jenis_kendaraan' => $slot->jenis_kendaraan,
                    'status_slot' => $slot->status_slot,
                    'is_tersedia' => $slot->status_slot === 'kosong',
                    'keterangan' => $slot->keterangan,
                    'active_vehicle' => $activeAssignment
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data slot parkir berhasil diambil',
                'count' => $data->count(),
                'summary' => [
                    'total' => $data->count(),
                    'kosong' => $data->where('status_slot', 'kosong')->count(),
                    'terisi' => $data->where('status_slot', 'terisi')->count(),
                    'reserved' => $data->where('status_slot', 'reserved')->count(),
                    'maintenance' => $data->where('status_slot', 'maintenance')->count(),
                    'non_aktif' => $data->where('status_slot', 'non_aktif')->count(),
                ],
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data slot: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail of a specific zone with all its slots and statuses.
     *
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showZone($id)
    {
        try {
            $zone = ParkingZone::withCount([
                'slots as total_slot',
                'slots as slot_kosong' => function ($q) {
                    $q->where('status_slot', 'kosong');
                },
                'slots as slot_terisi' => function ($q) {
                    $q->where('status_slot', 'terisi');
                }
            ])->with(['slots.activeAssignment'])->findOrFail($id);

            $slots = $zone->slots->map(function ($slot) {
                $activeAssignment = null;
                if ($slot->activeAssignment) {
                    $assign = $slot->activeAssignment;
                    $waktuMasuk = $assign->waktu_masuk ? Carbon::parse($assign->waktu_masuk) : null;
                    $activeAssignment = [
                        'id' => $assign->id,
                        'no_polisi' => $assign->no_polisi,
                        'jenis_kendaraan' => $assign->jenis_kendaraan,
                        'nama_driver' => $assign->nama_driver,
                        'no_hp_driver' => $assign->no_hp_driver,
                        'waktu_masuk' => $assign->waktu_masuk,
                        'durasi_parkir' => $waktuMasuk ? $waktuMasuk->diffForHumans(null, true) : null,
                        'status_assignment' => $assign->status_assignment,
                    ];
                }

                return [
                    'id' => $slot->id,
                    'kode_slot' => $slot->kode_slot,
                    'jenis_kendaraan' => $slot->jenis_kendaraan,
                    'status_slot' => $slot->status_slot,
                    'is_tersedia' => $slot->status_slot === 'kosong',
                    'active_vehicle' => $activeAssignment
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail zona parkir berhasil diambil',
                'data' => [
                    'id' => $zone->id,
                    'kode_zona' => $zone->kode_zona,
                    'nama_zona' => $zone->nama_zona,
                    'status_zona' => $zone->status,
                    'keterangan' => $zone->keterangan,
                    'total_slot' => (int) $zone->total_slot,
                    'slot_kosong' => (int) $zone->slot_kosong,
                    'slot_terisi' => (int) $zone->slot_terisi,
                    'slots' => $slots
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Zona parkir tidak ditemukan atau terjadi kesalahan: ' . $e->getMessage()
            ], 404);
        }
    }
}
