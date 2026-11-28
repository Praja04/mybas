<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpApprovalLog extends Model
{
    protected $table = 'sp_approval_logs';

    protected $fillable = [
        'sp_pelanggaran_id',
        'user_id',
        'action',
        'notes',
    ];

    const ACTION_SUBMIT = 'SUBMIT';
    const ACTION_DEPT_HEAD_APPROVE = 'DEPT_HEAD_APPROVE';
    const ACTION_DEPT_HEAD_REJECT = 'DEPT_HEAD_REJECT';
    const ACTION_IR_STAFF_SUBMIT = 'IR_STAFF_SUBMIT';
    const ACTION_IR_STAFF_REJECT = 'IR_STAFF_REJECT';
    const ACTION_IR_HEAD_APPROVE = 'IR_HEAD_APPROVE';
    const ACTION_IR_HEAD_REJECT = 'IR_HEAD_REJECT';
    const ACTION_REQUEST_CANCEL = 'REQUEST_CANCEL';
    const ACTION_CANCEL_APPROVE_DH = 'CANCEL_APPROVE_DH';
    const ACTION_CANCEL_APPROVE_IR = 'CANCEL_APPROVE_IR';
    const ACTION_CANCEL_FINAL = 'CANCEL_FINAL';
    const ACTION_CANCEL = 'CANCEL';
    const ACTION_EMAIL_SENT = 'EMAIL_SENT';

    public function spPelanggaran()
    {
        return $this->belongsTo(SpPelanggaran::class, 'sp_pelanggaran_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function logAction($spId, $userId, $action, $notes = null)
    {
        return self::create([
            'sp_pelanggaran_id' => $spId,
            'user_id' => $userId,
            'action' => $action,
            'notes' => $notes,
        ]);
    }
}
