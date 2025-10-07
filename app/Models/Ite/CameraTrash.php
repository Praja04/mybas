<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class CameraTrash extends Model
{
    protected $connection = 'ite';
    protected $table = 'cameras_trash';
    protected $guarded = [];
}
