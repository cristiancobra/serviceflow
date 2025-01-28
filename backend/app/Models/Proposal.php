<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Opportunity;
use App\Models\ProposalCost;
use App\Models\ProposalService;

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
        'total_profit',
        'total_profit_percentage',
        'total_discount',
        'total_price',
        'validity_days',
        'status',
    ];

    // relationships

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function proposalServices()
    {
        return $this->hasMany(ProposalService::class);
    }

    public function proposalCosts()
    {
        return $this->hasMany(ProposalCost::class);
    }

    // methods

    public static function getTotalValue()
    {
        return self::where('status', 'accepted')
            ->sum('total_price');
    }

    public static function getAcceptedCount()
    {
        return self::where('status', 'accepted')->count();
    }
}
