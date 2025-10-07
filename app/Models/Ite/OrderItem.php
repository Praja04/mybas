<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $connection = 'ite';
	protected $table = 'orders_items';
    protected $fillable = [
    	'material_id','order_id', 'quantity'
    ];

    public function material()
    {
    	return $this->belongsTo('App\Models\Ite\Material', 'material_id');
    }

    public function io()
    {
        return $this->belongsTo('App\Models\Ite\IO', 'io_id');
    }

}
