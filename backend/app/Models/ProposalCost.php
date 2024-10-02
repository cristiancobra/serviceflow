<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalCost extends Model
{
    use HasFactory;

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
