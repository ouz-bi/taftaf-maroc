<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'address_id', 'rider_id',
        'subtotal', 'delivery_fee', 'discount', 'total',
        'status', 'payment_method', 'payment_status', 'payment_reference',
        'confirmed_at', 'delivered_at', 'delivery_time', 'notes', 'cancellation_reason',
        'customer_name', 'customer_phone', 'delivery_address',
        'delivery_latitude', 'delivery_longitude',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'delivery_fee' => 'float',
        'discount' => 'float',
        'total' => 'float',
        'confirmed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'TF-' . strtoupper(uniqid());
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}
