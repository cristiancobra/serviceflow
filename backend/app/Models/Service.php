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
        'labor_hourly_total',
        'profit_percentage',
        'profit',
        'price',
        'observations',
    ];

    public function costs()
    {
        return $this->belongsToMany(Cost::class, 'service_costs')
            ->withPivot('quantity', 'price', 'total_price')
            ->withTimestamps();
    }

    public function calculateLaborHourlyTotal($service)
    {
        $hours = $service->labor_hours / 3600;
        $total = $hours * $service->labor_hourly_rate;
        $formattedTotal = number_format($total, 2, '.', '');

        return $formattedTotal;
    }


    public function calculatePrice($service)
    {
        return $service->labor_hourly_total + $service->profit;
    }

    public function calculateProfit($service)
    {
        $divisionFactorPercentual = 100 - $service->profit_percentage;
        $divisionFactorDecimal = $divisionFactorPercentual / 100;

        $profit = $service->labor_hourly_total / $divisionFactorDecimal - $service->labor_hourly_total;
        $profit = number_format($profit, 2);

        return $profit;
    }
}
