<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'account_id',
        'opportunity_id',
        'name', 
        'description', 
        'date', 
        'total_hours',
        'total_operational_cost',
        'total_third_party_cost',
        'profit_margin',  // Pode ser null
        'total_discount',
        'total_price',
        'validity_days'
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
