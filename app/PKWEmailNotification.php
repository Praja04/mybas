<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PKWEmailNotification extends Model
{
    protected $table = 'pkw_email_notification';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User', 'id_user');
    }
}
