<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'cart_id',
        'address_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // public function cart()
    // {
    //     return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    // }
}
