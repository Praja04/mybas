<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'ite';
    protected $fillable = [
    	'project_id', 'status', 'pr_number', 'pr_date', 'po_date', 'arrive_date', 'reservation_date'
    ];

    // public function materials()
    // {
    //     return $this->belongsToMany('App\Models\Ite\Material', 'orders_items', 'order_id', 'material_id')->withPivot('io_id', 'quantity', 'po', 'arrive');
    // }

    public function items()
    {
        return $this->hasMany('App\Models\Ite\OrderItem', 'order_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Ite\Project', 'project_id');
    }
}
