<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SpPelanggaran extends Model
{
    protected $table = 'sp_pelanggarans';

    protected $fillable = [
        'employee_id',
        'kode_admin',
        'kode_ir',
        'no_sp',
        'jenis_pelanggaran',
        'status',
        'alasan',
        'lampiran',
        'lampiran_cancel',
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
        'kategori_sp',
        'masa_berlaku_sampai',
        'is_active',
        'assigned_dept_head_id',
        'dept_head_approved_at',
        'dept_head_notes',
        'ir_staff_id',
        'ir_staff_notes',
        'ir_head_approved_at',
        'ir_head_notes',
        'nomor_sp_generated',
        'mangkir_ke',
        'bulan_mangkir',
        'email_sent',
        'email_sent_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->syncKlasifikasiFields();
        });
    }

    public function syncKlasifikasiFields()
    {
        $date = $this->tanggal_pelanggaran ? Carbon::parse($this->tanggal_pelanggaran) : Carbon::parse($this->created_at ?: now());
        $sampai = $date->copy()->addMonths(6);

        $this->masa_berlaku_sampai = $sampai->format('Y-m-d');

        if ($this->current_status === self::STATUS_CANCELLED) {
            $this->kategori_sp = 'CANCEL';
            $this->is_active = 0;
        } elseif (in_array($this->current_status, [self::STATUS_CANCEL_PENDING_DH, self::STATUS_CANCEL_PENDING_IR, self::STATUS_CANCEL_PENDING_IR_HEAD])) {
            $this->kategori_sp = 'PROSES_CANCEL';
            $this->is_active = 1; // Masih aktif sampai IR Head approve cancel
        } elseif ($this->current_status === self::STATUS_REJECTED) {
            $this->kategori_sp = 'DITOLAK';
            $this->is_active = 0;
        } elseif ($this->current_status === self::STATUS_APPROVED) {
            if ($sampai->isPast()) {
                $this->kategori_sp = 'EXPIRED';
                $this->is_active = 0;
            } elseif (in_array($this->jenis_pelanggaran, ['SP 3', 'Surat Peringatan 3 (SP 3)'])) {
                $this->kategori_sp = 'SP3';
                $this->is_active = 1;
            } else {
                $this->kategori_sp = 'AKTIF';
                $this->is_active = 1;
            }
        } else {
            $this->kategori_sp = 'PROSES';
            $this->is_active = 0;
        }
    }

    // Status constants
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_PENDING_DH = 'PENDING_DH';
    const STATUS_PENDING_IR = 'PENDING_IR';
    const STATUS_PENDING_IR_HEAD = 'PENDING_IR_HEAD';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_CANCELLED = 'CANCELLED';

    // Cancel workflow statuses (for already issued SPs)
    const STATUS_CANCEL_PENDING_DH = 'CANCEL_PENDING_DH';
    const STATUS_CANCEL_PENDING_IR = 'CANCEL_PENDING_IR';
    const STATUS_CANCEL_PENDING_IR_HEAD = 'CANCEL_PENDING_IR_HEAD';

    /**
     * Check if SP is active (APPROVED and within 6 months of validity)
     */
    public function isActiveSp()
    {
        if ($this->current_status !== self::STATUS_APPROVED) {
            return false;
        }
        $date = $this->tanggal_pelanggaran ? Carbon::parse($this->tanggal_pelanggaran) : Carbon::parse($this->updated_at);
        return $date->copy()->addMonths(6)->isFuture() || $date->copy()->addMonths(6)->isToday();
    }

    /**
     * Check if SP is expired (> 6 months after issue)
     */
    public function isExpiredSp()
    {
        if ($this->current_status !== self::STATUS_APPROVED) {
            return false;
        }
        $date = $this->tanggal_pelanggaran ? Carbon::parse($this->tanggal_pelanggaran) : Carbon::parse($this->updated_at);
        return $date->copy()->addMonths(6)->isPast();
    }

    /**
     * Get 5-classification status text
     */
    public function getKlasifikasiStatusAttribute()
    {
        if ($this->current_status === self::STATUS_CANCELLED) {
            return 'CANCEL';
        }
        if ($this->current_status === self::STATUS_REJECTED) {
            return 'DITOLAK';
        }
        if ($this->current_status === self::STATUS_APPROVED) {
            if ($this->isExpiredSp()) {
                return 'TIDAK AKTIF (EXPIRED)';
            }
            if (in_array($this->jenis_pelanggaran, ['SP 3', 'Surat Peringatan 3 (SP 3)'])) {
                return 'SP+3';
            }
            return 'AKTIF';
        }
        return 'PROSES (' . $this->current_status . ')';
    }

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

    public function dates()
    {
        return $this->hasMany(SpPelanggaranDate::class, 'sp_pelanggaran_id')->orderBy('tanggal', 'asc');
    }

    /**
     * Convert month number to Roman numeral
     */
    public static function numberToRoman($number)
    {
        $map = [
            12 => 'XII', 11 => 'XI', 10 => 'X', 9 => 'IX',
            8  => 'VIII', 7  => 'VII', 6 => 'VI', 5 => 'V',
            4  => 'IV', 3  => 'III', 2 => 'II', 1 => 'I'
        ];
        return $map[(int)$number] ?? 'I';
    }

    /**
     * Generate nomor SP dengan format: No. {URUT}/SP/{KODE_DEPT}/{BULAN_ROMAWI}/{TAHUN}
     * Contoh: No. 64/SP/IER/VII/2026
     */
    public static function generateNomorSp($employeeId = null)
    {
        $currentYear = Carbon::now()->format('Y');
        $bulanRomawi = self::numberToRoman(Carbon::now()->format('n'));

        // Query all generated SPs in the current year
        $approvedSpsThisYear = self::whereNotNull('nomor_sp_generated')
            ->where(function($q) use ($currentYear) {
                $q->whereYear('ir_head_approved_at', $currentYear)
                  ->orWhereYear('created_at', $currentYear)
                  ->orWhere('nomor_sp_generated', 'like', "%/{$currentYear}");
            })
            ->get();

        $maxSeq = 0;
        foreach ($approvedSpsThisYear as $sp) {
            if (preg_match('/(?:No\.\s*|^)(\d+)\/SP\//i', $sp->nomor_sp_generated, $matches)) {
                $seq = intval($matches[1]);
                if ($seq > $maxSeq) {
                    $maxSeq = $seq;
                }
            }
        }

        $nextSeq = $maxSeq + 1;

        return "{$nextSeq}/SP/IR/{$bulanRomawi}/{$currentYear}";
    }

    public function getTanggalPelanggaranAttribute()
    {
        $firstDate = $this->dates ? $this->dates->first() : null;
        if ($firstDate) {
            return $firstDate->tanggal;
        }
        return $this->created_at ? $this->created_at->format('Y-m-d') : date('Y-m-d');
    }

    public function canSubmitToDeptHead()
    {
        return in_array($this->current_status, [self::STATUS_DRAFT, self::STATUS_REJECTED]) 
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
