<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category',
        'name',
        'labor_hours',
        'labor_hourly_rate',
        'labor_hourly_rate_total',
        'profit_percentage',
        'price',
        'observations',
    ];
}
