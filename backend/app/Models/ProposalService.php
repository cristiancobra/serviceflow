<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProposalService extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'proposal_services';

    protected $fillable = [
        'id',
        'proposal_id',
        'service_id',
        'account_id',
        'name',
        'quantity',
        'category',
        'labor_hours',
        'labor_hourly_rate',
        'labor_hourly_rate_total',
        'profit_percentage',
        'labor_hours_total',
        'price',
        'profit',
        'total_profit',
        'total_price',
        'observations',
    ];

    // Define as relações com outros modelos
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
