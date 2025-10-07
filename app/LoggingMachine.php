<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoggingMachine extends Model
{
    public $timestamps = false;

    protected $table = 'logging_machine';

    protected $guarded = [];
}
