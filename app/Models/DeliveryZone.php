<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'polygon_coordinates',
        'delivery_fee',
        'estimated_time',
        'is_active',
    ];

    protected $casts = [
        'polygon_coordinates' => 'array',
        'is_active' => 'boolean',
    ];
}
