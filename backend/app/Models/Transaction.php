<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'invoice_id',
        'bank_account_id',
        'amount',
        'transaction_date',
        'type',
        'method',
    ];

    // relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    // Events
    protected static function booted()
    {
        // When a transaction is created
        static::created(function ($transaction) {
            if ($transaction->invoice_id) {
                $transaction->invoice->updateTotalPaid();
            }
        });

        // When a transaction is updated
        static::updated(function ($transaction) {
            if ($transaction->invoice_id) {
                $transaction->invoice->updateTotalPaid();
            }
        });

        // When a transaction is deleted
        static::deleted(function ($transaction) {
            if ($transaction->invoice_id) {
                $transaction->invoice->updateTotalPaid();
            }
        });
    }
}
