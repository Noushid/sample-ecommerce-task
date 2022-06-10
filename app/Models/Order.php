<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['orderid', 'customer_name', 'phone', 'product_id', 'quantity', 'net_amount'];

//    public function products()
//    {
//        return $this->belongsToMany(OrderProduct::class,'order_products', 'order_id', 'product_id');
//    }

    public static function boot() {

        parent::boot();

        static::deleting(function ($order) {
            OrderProduct::where('order_id', $order->id)->delete();
        });
    }

}
