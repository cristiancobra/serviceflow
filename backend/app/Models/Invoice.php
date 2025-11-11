<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'proposal_id',
        'user_id',
        'date_due',
        'price',
        'total_paid',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Recalculate and update the total_paid based on transactions
     */
    public function updateTotalPaid()
    {
        $totalPaid = $this->transactions()->sum('amount');
        $this->update(['total_paid' => $totalPaid]);
        return $totalPaid;
    }
}
