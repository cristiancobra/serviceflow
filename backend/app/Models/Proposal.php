<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Opportunity;
use App\Models\ProposalItem;

class Proposal extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'id',
        'account_id',
        'opportunity_id',
        'name', 
        'description', 
        'date', 
        'total_hours',
        'total_operational_cost',
        'total_third_party_cost',
        'profit_margin',  // Pode ser null
        'total_profit',
        'total_discount',
        'total_price',
        'validity_days'
    ];

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function proposalItems()
    {
        return $this->hasMany(ProposalItem::class);
    }
}
