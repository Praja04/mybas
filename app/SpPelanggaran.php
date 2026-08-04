<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SpPelanggaran extends Model
{
    protected $table = 'sp_pelanggarans';

    protected $fillable = [
        'employee_id',
        'no_sp',
        'tanggal_pelanggaran',
        'jenis_pelanggaran',
        'status',
        'alasan',
        'lampiran',
        'sesuai_ketentuan',
        'reported_to_admin',
        'created_by_user_id',
        'email_dept_head',
        'email_dept_hr',
        'email_dept_user',
        // Approval fields
        'sumber_data',
        'pasal_dilanggar',
        'uraian_pelanggaran',
        'current_status',
        'assigned_dept_head_id',
        'dept_head_approved_at',
        'dept_head_notes',
        'ir_staff_id',
        'ir_staff_notes',
        'ir_head_approved_at',
        'ir_head_notes',
        'nomor_sp_generated',
        'email_sent',
        'email_sent_at',
    ];

    // Status constants
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_PENDING_DH = 'PENDING_DH';
    const STATUS_PENDING_IR = 'PENDING_IR';
    const STATUS_PENDING_IR_HEAD = 'PENDING_IR_HEAD';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    public function employee()
    {
        return $this->belongsTo(HrKaryawan::class, 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function deptHead()
    {
        return $this->belongsTo(User::class, 'assigned_dept_head_id');
    }

    public function irStaff()
    {
        return $this->belongsTo(User::class, 'ir_staff_id');
    }

    public function irHead()
    {
        return $this->belongsTo(User::class, 'ir_head_approved_id', 'id');
    }

    public function approvalLogs()
    {
        return $this->hasMany(SpApprovalLog::class, 'sp_pelanggaran_id')->orderBy('created_at', 'desc');
    }

    /**
     * Generate nomor SP dengan format: SP-{KODE_DIVISI}/{MMYYYY}/{NNN}
     */
    public static function generateNomorSp($employeeId)
    {
        $employee = HrKaryawan::findOrFail($employeeId);
        $kodeDept = $employee->kode_divisi ?? $employee->kode_bagian ?? 'UNK';

        $bulan = Carbon::now()->format('m');
        $tahun = Carbon::now()->format('Y');
        $bulanTahun = $bulan . $tahun;

        $lastSp = self::where('nomor_sp_generated', 'like', "SP-{$kodeDept}/{$bulanTahun}%")
            ->orderBy('nomor_sp_generated', 'desc')
            ->first();

        if ($lastSp && preg_match('/\/(\d{3})$/', $lastSp->nomor_sp_generated, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        return "SP-{$kodeDept}/{$bulanTahun}/{$formattedNumber}";
    }

    public function canSubmitToDeptHead()
    {
        return in_array($this->current_status, [self::STATUS_DRAFT, self::STATUS_REJECTED]) 
            && !empty($this->jenis_pelanggaran)
            && !empty($this->tanggal_pelanggaran)
            && !empty($this->employee_id);
    }

    public function canDeptHeadReview()
    {
        return $this->current_status === self::STATUS_PENDING_DH;
    }

    public function canIrStaffReview()
    {
        return $this->current_status === self::STATUS_PENDING_IR;
    }

    public function canIrHeadReview()
    {
        return $this->current_status === self::STATUS_PENDING_IR_HEAD;
    }

    public static function getIrTeamEmails()
    {
        return User::whereIn('user_role', ['ir_staff', 'ir_head'])
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();
    }

    public static function getDeptHeadEmails()
    {
        return User::where('user_role', 'dept_head')
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();
    }
}
