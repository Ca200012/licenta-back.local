<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cart_id',
        'address_id',
        'status'
    ];



    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d-m-Y H:i');
    }
}
