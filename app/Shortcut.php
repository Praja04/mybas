<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shortcut extends Model
{
    protected $fillable = ['name','link','icon','status','front'];
}
