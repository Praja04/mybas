<?php

namespace App\Http\Middleware;

use App\Models\HR\HrPmsActivityLog;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Audit log khusus modul PMS (IPP & PK):
 * mencatat siapa melakukan aksi apa (view/save/approve/export/locking/...)
 * pada route mana, beserta target (nik/id) yang disentuh.
 *
 * Endpoint polling (list/teamList/diskusiList/locking*Get/normalScaleGet)
 * tidak dicatat agar log tidak banjir.
 */
class PmsActivityLogMiddleware
{
    private const SKIP_METHODS = [
        'list',
        'teamList',
        'diskusiList',
        'lockingPascaleGet',
        'lockingIppGet',
        'lockingPkGet',
        'normalScaleGet',
    ];

    private const METHOD_ACTION = [
        'index' => 'view',
        'show' => 'view',
        'detail' => 'view',
        'diskusiIndex' => 'view',
        'export' => 'export',
        'exportPdf' => 'export',
        'exportForemanPdf' => 'export',
        'exportIpp' => 'export',
        'exportIppForeman' => 'export',
        'download' => 'download',
        'save' => 'save',
        'saveAktual' => 'save',
        'saveAktualDepartemen' => 'save',
        'normalScaleSave' => 'save',
        'approve' => 'approve',
        'review' => 'review',
        'diskusiStore' => 'discussion',
        'diskusiUpdate' => 'discussion',
        'diskusiAnswer' => 'discussion',
        'diskusiAnswerUpdate' => 'discussion',
        'lockingPascaleSave' => 'locking',
        'lockingIppSave' => 'locking',
        'lockingPkSave' => 'locking',
        'paFinalSave' => 'pa-calibrate',
        'paFinalBulkSave' => 'pa-calibrate',
        'notifikasiSave' => 'notification',
        'normalScaleReset' => 'reset',
        'upload' => 'upload',
        'delete' => 'delete',
    ];

    private const ACTION_LABELS = [
        'view' => 'Melihat halaman',
        'export' => 'Export data',
        'download' => 'Download berkas',
        'save' => 'Menyimpan data',
        'approve' => 'Persetujuan / approval',
        'review' => 'Review data',
        'discussion' => 'Diskusi',
        'locking' => 'Locking / unlock',
        'pa-calibrate' => 'PA Scale Calibrated',
        'notification' => 'Pengaturan notifikasi',
        'reset' => 'Reset konfigurasi',
        'upload' => 'Upload berkas',
        'delete' => 'Menghapus data',
    ];

    private const META_KEYS = [
        'status',
        'tahapan',
        'periode',
        'mode',
        'tab',
        'stage',
        'is_active',
        'action',
        'otorisasi_id',
    ];

    public function handle($request, Closure $next)
    {
        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            try {
                $this->log($request, null, $e);
            } catch (\Throwable $logError) {
                report($logError);
            }

            throw $e;
        }

        try {
            $this->log($request, $response, null);
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }

    private function log($request, $response = null, $exception = null): void
    {
        $route = $request->route();
        if (!$route) {
            return;
        }

        $httpMethod = strtoupper((string) $request->getMethod());
        if (!in_array($httpMethod, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }

        $routeAction = (string) $route->getActionName();
        $method = (string) $route->getActionMethod();

        if ($httpMethod === 'GET' && in_array($method, self::SKIP_METHODS, true)) {
            return;
        }

        $action = self::METHOD_ACTION[$method] ?? ($httpMethod === 'GET' ? 'view' : 'action');

        $params = [];
        foreach ((array) $route->parameters() as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $params[$key] = $value;
            }
        }

        $meta = [];
        foreach (self::META_KEYS as $key) {
            $value = $request->input($key);
            if (is_scalar($value) && $value !== '') {
                $meta[$key] = is_string($value) ? mb_substr($value, 0, 100) : $value;
            }
        }
        if ($params) {
            $meta['params'] = $params;
        }

        $user = Auth::user();

        HrPmsActivityLog::record([
            'user_id' => $user ? $user->id : null,
            'user_nik' => $user && !empty($user->nik) ? mb_substr(trim((string) $user->nik), 0, 50) : null,
            'user_username' => $user && !empty($user->username) ? mb_substr(trim((string) $user->username), 0, 100) : null,
            'user_name' => $user && !empty($user->name) ? mb_substr(trim((string) $user->name), 0, 150) : null,
            'module' => mb_substr($this->moduleOf($request), 0, 60),
            'action' => mb_substr($action, 0, 30),
            'action_label' => mb_substr(self::ACTION_LABELS[$action] ?? ucfirst($action), 0, 150),
            'route_action' => mb_substr($routeAction, 0, 150),
            'http_method' => mb_substr($httpMethod, 0, 10),
            'uri' => mb_substr((string) $request->getRequestUri(), 0, 500),
            'target_type' => $this->targetTypeOf($params),
            'target_id' => $this->targetIdOf($params),
            'target_nik' => isset($params['nik']) ? mb_substr(trim((string) $params['nik']), 0, 50) : null,
            'meta' => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
            'status_code' => $this->statusCodeOf($response, $exception),
            'ip_address' => mb_substr((string) $request->ip(), 0, 45),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
        ]);
    }

    private function statusCodeOf($response, $exception = null): ?int
    {
        if ($response && method_exists($response, 'getStatusCode')) {
            return (int) $response->getStatusCode();
        }
        if ($exception instanceof HttpExceptionInterface) {
            return (int) $exception->getStatusCode();
        }
        if ($exception instanceof ModelNotFoundException) {
            return 404;
        }
        if ($exception instanceof ValidationException) {
            return 422;
        }
        if ($exception instanceof AuthorizationException) {
            return 403;
        }
        if ($exception instanceof AuthenticationException) {
            return 401;
        }

        return $exception ? 500 : null;
    }

    private function moduleOf($request): string
    {
        $path = trim((string) $request->path(), '/');
        $segments = explode('/', $path);

        return (string) ($segments[1] ?? $segments[0] ?? 'pms');
    }

    private function targetTypeOf(array $params): ?string
    {
        if (isset($params['nik'])) {
            return 'nik';
        }
        if (isset($params['pkId'])) {
            return 'pk';
        }
        if (isset($params['ippId']) || isset($params['otoId'])) {
            return 'ipp';
        }
        if (isset($params['id'])) {
            return 'id';
        }

        return null;
    }

    private function targetIdOf(array $params): ?int
    {
        foreach (['id', 'pkId', 'ippId', 'otoId'] as $key) {
            if (isset($params[$key]) && is_numeric($params[$key])) {
                return (int) $params[$key];
            }
        }

        return null;
    }
}
