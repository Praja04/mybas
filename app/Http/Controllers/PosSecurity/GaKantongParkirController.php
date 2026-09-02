<?php

namespace App\Http\Controllers\PosSecurity;

use App\Http\Controllers\Controller;
use App\Models\PosSecurity\KantongParkir\ParkingZone;
use App\Models\PosSecurity\KantongParkir\ParkingSlot;
use App\Models\PosSecurity\KantongParkir\ParkingAssignment;
use App\Models\PosSecurity\KantongParkir\ParkingSlotStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GaKantongParkirController extends Controller
{
    public function index()
    {
        $zones = ParkingZone::orderBy('kode_zona', 'asc')->get();
        return view('pos-security.master.kantong-parkir.index', compact('zones'));
    }

    // --- PARKING ZONES (UTAMA) ---

    public function getZones(Request $request)
    {
        $query = ParkingZone::withCount([
            'slots as total_slots',
            'slots as filled_slots' => function ($q) {
                $q->where('status_slot', 'terisi');
            },
            'slots as empty_slots' => function ($q) {
                $q->where('status_slot', 'kosong');
            }
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_zona', 'like', "%{$search}%")
                    ->orWhere('nama_zona', 'like', "%{$search}%")
                    ->orWhere('lokasi_detail', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $zones = $query->orderBy('kode_zona', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $zones
        ]);
    }

    public function storeZone(Request $request)
    {
        $id = $request->id;

        $request->validate([
            'kode_zona' => 'required|string|max:50|unique:parking_zones,kode_zona,' . $id,
            'nama_zona' => 'required|string|max:150',
            'status' => 'required|in:aktif,non_aktif,maintenance',
        ], [
            'kode_zona.required' => 'Kode Zona harus diisi.',
            'kode_zona.unique' => 'Kode Zona sudah digunakan.',
            'nama_zona.required' => 'Nama Zona harus diisi.',
        ]);

        try {
            $data = [
                'kode_zona' => strtoupper(trim($request->kode_zona)),
                'nama_zona' => $request->nama_zona,
                'kapasitas_total' => $request->kapasitas_total ?? 0,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ];

            if ($id) {
                $data['updated_by'] = Auth::id();
                $zone = ParkingZone::findOrFail($id);
                $zone->update($data);
                $msg = 'Data Zona Parkir berhasil diperbarui.';
            } else {
                $data['created_by'] = Auth::id();
                $zone = ParkingZone::create($data);
                $msg = 'Zona Parkir baru berhasil ditambahkan.';
            }

            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'data' => $zone
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan zona: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showZone($id)
    {
        $zone = ParkingZone::with('slots')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $zone
        ]);
    }

    public function destroyZone($id)
    {
        try {
            $zone = ParkingZone::findOrFail($id);
            $zone->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Zona Parkir berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus zona: ' . $e->getMessage()
            ], 500);
        }
    }

    // --- PARKING SLOTS (DETAIL) ---

    public function getSlots(Request $request)
    {
        $query = ParkingSlot::with(['zone', 'activeAssignment']);

        if ($request->filled('parking_zone_id')) {
            $query->where('parking_zone_id', $request->parking_zone_id);
        }

        if ($request->filled('status_slot')) {
            $query->where('status_slot', $request->status_slot);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_slot', 'like', "%{$search}%")
                    ->orWhere('nama_slot', 'like', "%{$search}%")
                    ->orWhere('jenis_kendaraan', 'like', "%{$search}%");
            });
        }

        $slots = $query->orderBy('kode_slot', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $slots
        ]);
    }

    public function storeSlot(Request $request)
    {
        $id = $request->id;

        $request->validate([
            'parking_zone_id' => 'required|exists:parking_zones,id',
            'kode_slot' => 'required|string|max:50',
            'status_slot' => 'required|in:kosong,terisi,reserved,maintenance,non_aktif',
        ], [
            'parking_zone_id.required' => 'Pilih Zona Parkir terlebih dahulu.',
            'kode_slot.required' => 'Kode Slot harus diisi.',
        ]);

        // Unique check per zone
        $exists = ParkingSlot::where('parking_zone_id', $request->parking_zone_id)
            ->where('kode_slot', strtoupper(trim($request->kode_slot)))
            ->when($id, function ($q) use ($id) {
                $q->where('id', '!=', $id);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode Slot ini sudah ada di dalam Zona yang dipilih.'
            ], 422);
        }

        try {
            $jenisKendaraan = is_array($request->jenis_kendaraan)
                ? implode(', ', $request->jenis_kendaraan)
                : $request->jenis_kendaraan;

            $data = [
                'parking_zone_id' => $request->parking_zone_id,
                'kode_slot' => strtoupper(trim($request->kode_slot)),
                'jenis_kendaraan' => $jenisKendaraan,
                'status_slot' => $request->status_slot,
                'keterangan' => $request->keterangan,
            ];

            if ($id) {
                $slot = ParkingSlot::findOrFail($id);
                $oldStatus = $slot->status_slot;
                $data['updated_by'] = Auth::id();
                $slot->update($data);

                if ($oldStatus !== $request->status_slot) {
                    ParkingSlotStatusHistory::create([
                        'parking_slot_id' => $slot->id,
                        'status_sebelumnya' => $oldStatus,
                        'status_baru' => $request->status_slot,
                        'keterangan' => 'Perubahan status manual oleh user',
                        'created_by' => Auth::id()
                    ]);
                }
                $msg = 'Slot Parkir berhasil diperbarui.';
            } else {
                $data['created_by'] = Auth::id();
                $slot = ParkingSlot::create($data);

                ParkingSlotStatusHistory::create([
                    'parking_slot_id' => $slot->id,
                    'status_sebelumnya' => null,
                    'status_baru' => $request->status_slot,
                    'keterangan' => 'Pembuatan slot parkir baru',
                    'created_by' => Auth::id()
                ]);

                // Update total capacity in zone
                $zone = ParkingZone::find($request->parking_zone_id);
                if ($zone) {
                    $zone->update(['kapasitas_total' => $zone->slots()->count()]);
                }

                $msg = 'Slot Parkir baru berhasil ditambahkan.';
            }

            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'data' => $slot
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan slot parkir: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateSlots(Request $request)
    {
        $request->validate([
            'parking_zone_id' => 'required|exists:parking_zones,id',
            'prefix' => 'required|string|max:10', // e.g. A
            'start_number' => 'required|integer|min:1',
            'end_number' => 'required|integer|min:1|gte:start_number',
        ]);

        try {
            DB::beginTransaction();

            $zoneId = $request->parking_zone_id;
            $prefix = strtoupper(trim($request->prefix));
            $createdCount = 0;

            for ($i = $request->start_number; $i <= $request->end_number; $i++) {
                $kodeSlot = $prefix . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);

                $exists = ParkingSlot::where('parking_zone_id', $zoneId)
                    ->where('kode_slot', $kodeSlot)
                    ->exists();

                if (!$exists) {
                    $slot = ParkingSlot::create([
                        'parking_zone_id' => $zoneId,
                        'kode_slot' => $kodeSlot,
                        'jenis_kendaraan' => $request->jenis_kendaraan ? implode(', ', $request->jenis_kendaraan) : null,
                        'status_slot' => 'kosong',
                        'created_by' => Auth::id()
                    ]);

                    ParkingSlotStatusHistory::create([
                        'parking_slot_id' => $slot->id,
                        'status_baru' => 'kosong',
                        'keterangan' => 'Generate bulk slot',
                        'created_by' => Auth::id()
                    ]);

                    $createdCount++;
                }
            }

            // Sync total capacity
            $zone = ParkingZone::find($zoneId);
            if ($zone) {
                $zone->update(['kapasitas_total' => $zone->slots()->count()]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil memproduksi {$createdCount} slot parkir baru."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal me-generate slot: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showSlot($id)
    {
        $slot = ParkingSlot::with(['zone', 'histories.assignment', 'activeAssignment'])->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $slot
        ]);
    }

    public function destroySlot($id)
    {
        try {
            $slot = ParkingSlot::findOrFail($id);
            $zoneId = $slot->parking_zone_id;
            $slot->delete();

            $zone = ParkingZone::find($zoneId);
            if ($zone) {
                $zone->update(['kapasitas_total' => $zone->slots()->count()]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Slot Parkir berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus slot: ' . $e->getMessage()
            ], 500);
        }
    }

    // --- PARKING ASSIGNMENT (PENUGASAN KENDARAAN REGISTRASI) ---

    public function assignParking(Request $request)
    {
        $request->validate([
            'parking_slot_id' => 'required|exists:parking_slots,id',
            'no_polisi' => 'required|string|max:20',
        ], [
            'parking_slot_id.required' => 'Pilih slot parkir.',
            'no_polisi.required' => 'Nomor polisi kendaraan harus diisi.',
        ]);

        try {
            DB::beginTransaction();

            $slot = ParkingSlot::findOrFail($request->parking_slot_id);

            if ($slot->status_slot === 'terisi') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Slot parkir ini sedang terisi oleh kendaraan lain.'
                ], 422);
            }

            $assignment = ParkingAssignment::create([
                'parking_zone_id' => $slot->parking_zone_id,
                'parking_slot_id' => $slot->id,
                'no_polisi' => strtoupper(trim($request->no_polisi)),
                'jenis_kendaraan' => $request->jenis_kendaraan,
                'nama_driver' => $request->nama_driver,
                'no_hp_driver' => $request->no_hp_driver,
                'visitor_transaction_id' => $request->visitor_transaction_id,
                'waktu_masuk' => now(),
                'status_assignment' => 'assigned',
                'catatan' => $request->catatan,
                'created_by' => Auth::id()
            ]);

            $oldStatus = $slot->status_slot;
            $slot->update(['status_slot' => 'terisi', 'updated_by' => Auth::id()]);

            ParkingSlotStatusHistory::create([
                'parking_slot_id' => $slot->id,
                'parking_assignment_id' => $assignment->id,
                'status_sebelumnya' => $oldStatus,
                'status_baru' => 'terisi',
                'keterangan' => 'Penugasan kendaraan ' . $assignment->no_polisi,
                'created_by' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kendaraan ' . $assignment->no_polisi . ' berhasil ditempatkan di slot ' . $slot->kode_slot,
                'data' => $assignment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal penugasan parkir: ' . $e->getMessage()
            ], 500);
        }
    }

    public function releaseParking($id)
    {
        try {
            DB::beginTransaction();

            $assignment = ParkingAssignment::findOrFail($id);
            $assignment->update([
                'waktu_keluar' => now(),
                'status_assignment' => 'completed',
                'updated_by' => Auth::id()
            ]);

            $slot = ParkingSlot::find($assignment->parking_slot_id);
            if ($slot) {
                $oldStatus = $slot->status_slot;
                $slot->update(['status_slot' => 'kosong', 'updated_by' => Auth::id()]);

                ParkingSlotStatusHistory::create([
                    'parking_slot_id' => $slot->id,
                    'parking_assignment_id' => $assignment->id,
                    'status_sebelumnya' => $oldStatus,
                    'status_baru' => 'kosong',
                    'keterangan' => 'Kendaraan ' . $assignment->no_polisi . ' keluar dari slot',
                    'created_by' => Auth::id()
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kendaraan ' . $assignment->no_polisi . ' telah selesai dan slot parkir kembali kosong.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal pelepasan parkir: ' . $e->getMessage()
            ], 500);
        }
    }
}
