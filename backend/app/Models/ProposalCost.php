<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToAccount;

class ProposalCost extends Model
{
    use HasFactory, BelongsToAccount;

    protected $fillable = [
        'proposal_id',
        'cost_id',
        'account_id',
        'name',
        'quantity',
        'price',
        'total_price',
        'observations'
    ];
}
