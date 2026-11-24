<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\HrPmsActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PmsActivityLogController extends Controller
{
    private function checkPermission(string $codename): void
    {
        $userCodenames = Auth::user()->getAllPermissionCodenames();
        if (!in_array($codename, $userCodenames, true)) {
            abort(403);
        }
    }

    private const MODULES = [
        'ipp-aktual' => 'IPP Aktual (Peserta)',
        'ipp-user-approval' => 'Persetujuan IPP (Atasan)',
        'ipp-approval' => 'IPP Approval (Admin HR)',
        'ipp-bukti' => 'Bukti File IPP',
        'pk-assessment' => 'Penilaian PK',
        'pk-assessment-hr' => 'Monitoring PK (Admin HR)',
        'pms-total-scoring' => 'PMS Total Scoring',
        'pms-audit-log' => 'Log Aktivitas PMS',
        'pms-access-otp' => 'Akses OTP PMS',
    ];

    private const ACTIONS = [
        'view' => 'Melihat halaman',
        'save' => 'Menyimpan data',
        'approve' => 'Persetujuan / approval',
        'review' => 'Review data',
        'export' => 'Export data',
        'download' => 'Download berkas',
        'upload' => 'Upload berkas',
        'delete' => 'Menghapus data',
        'discussion' => 'Diskusi',
        'locking' => 'Locking / unlock',
        'pa-calibrate' => 'PA Scale Calibrated',
        'notification' => 'Pengaturan notifikasi',
        'reset' => 'Reset konfigurasi',
        'otp-request' => 'Permintaan OTP akses PMS',
        'otp-send' => 'Mengirim OTP akses PMS',
        'otp-verify' => 'Verifikasi OTP akses PMS berhasil',
        'otp-fail' => 'Verifikasi OTP akses PMS gagal',
    ];

    private const STATUSES = [
        'risk' => 'Berisiko (4xx–5xx)',
        '401' => '401 Unauthorized',
        '403' => '403 Akses ditolak',
        '404' => '404 Tidak ditemukan',
        '422' => '422 Ditolak / validasi',
        '500' => '500 Error server',
        'success' => 'Sukses (2xx)',
    ];

    public function index()
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        return view('hr.pms-audit-log.pms-audit-log', [
            'modules' => self::MODULES,
            'actions' => self::ACTIONS,
            'statuses' => self::STATUSES,
            'maxRows' => HrPmsActivityLog::MAX_ROWS,
        ]);
    }

    public function list(Request $request)
    {
        $this->checkPermission('hr_upload_file_template_ipp');

        $perPage = (int) $request->get('per_page', 20);
        $perPage = max(1, min(100, $perPage));
        $page = max(1, (int) $request->get('page', 1));
        $fUser = trim((string) $request->get('user', ''));
        $fModule = trim((string) $request->get('module', ''));
        $fAction = trim((string) $request->get('action', ''));
        $fTarget = trim((string) $request->get('target_nik', ''));
        $fFrom = trim((string) $request->get('date_from', ''));
        $fTo = trim((string) $request->get('date_to', ''));
        $fStatus = trim((string) $request->get('status', ''));

        $query = HrPmsActivityLog::query()->orderBy('created_at', 'desc')->orderBy('id', 'desc');

        if ($fUser !== '') {
            $query->where(function ($q) use ($fUser) {
                $q->where('user_nik', 'like', '%' . $fUser . '%')
                    ->orWhere('user_username', 'like', '%' . $fUser . '%')
                    ->orWhere('user_name', 'like', '%' . $fUser . '%');
            });
        }
        if ($fModule !== '') {
            $query->where('module', $fModule);
        }
        if ($fAction !== '') {
            $query->where('action', $fAction);
        }
        if ($fTarget !== '') {
            $query->where('target_nik', 'like', '%' . $fTarget . '%');
        }
        if ($fStatus === 'risk') {
            $query->where('status_code', '>=', 400);
        } elseif ($fStatus === 'success') {
            $query->whereBetween('status_code', [200, 299]);
        } elseif ($fStatus !== '' && ctype_digit($fStatus)) {
            $query->where('status_code', (int) $fStatus);
        }
        if ($fFrom !== '') {
            $query->where('created_at', '>=', $fFrom . ' 00:00:00');
        }
        if ($fTo !== '') {
            $query->where('created_at', '<=', $fTo . ' 23:59:59');
        }

        $total = (clone $query)->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $rows = $query->forPage($page, $perPage)->get();

        $data = $rows->map(function ($row) {
            $meta = $row->meta;
            if (is_string($meta)) {
                $decoded = json_decode($meta, true);
                $meta = is_array($decoded) ? $decoded : null;
            }

            return [
                'id' => $row->id,
                'created_at' => $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : null,
                'user_name' => $row->user_name,
                'user_nik' => $row->user_nik,
                'user_username' => $row->user_username,
                'module' => $row->module,
                'module_label' => self::MODULES[$row->module] ?? $row->module,
                'action' => $row->action,
                'action_label' => self::ACTIONS[$row->action] ?? $row->action_label,
                'route_action' => $row->route_action,
                'http_method' => $row->http_method,
                'uri' => $row->uri,
                'target_type' => $row->target_type,
                'target_id' => $row->target_id,
                'target_nik' => $row->target_nik,
                'meta' => $meta,
                'status_code' => $row->status_code,
                'ip_address' => $row->ip_address,
                'user_agent' => $row->user_agent,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => ['page' => $page, 'per_page' => $perPage, 'total' => $total, 'last_page' => $lastPage],
            'filterOptions' => [
                'modules' => collect(self::MODULES)->map(fn ($label, $key) => ['v' => $key, 't' => $label])->values(),
                'actions' => collect(self::ACTIONS)->map(fn ($label, $key) => ['v' => $key, 't' => $label])->values(),
                'statuses' => collect(self::STATUSES)->map(fn ($label, $key) => ['v' => $key, 't' => $label])->values(),
            ],
        ]);
    }
}
