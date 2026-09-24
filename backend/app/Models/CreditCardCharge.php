<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToAccount;

class CreditCardCharge extends Model
{
    use HasFactory, SoftDeletes, BelongsToAccount;

    protected $fillable = [
        'account_id',
        'credit_card_id',
        'credit_card_invoice_id',
        'description',
        'amount',
        'purchase_date',
        'category',
        'installment_number',
        'installment_total',
        'installment_group_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function creditCard()
    {
        return $this->belongsTo(CreditCard::class);
    }

    public function creditCardInvoice()
    {
        return $this->belongsTo(CreditCardInvoice::class);
    }

    /**
     * Transaction que gerou esta compra ao pagar uma invoice com o método
     * "cartão de crédito". Nula para compras lançadas diretamente no cartão.
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    protected static function booted()
    {
        static::created(function ($charge) {
            $charge->creditCardInvoice->updateTotalAmount();
        });

        static::updated(function ($charge) {
            $charge->creditCardInvoice->updateTotalAmount();
        });

        static::deleted(function ($charge) {
            $charge->creditCardInvoice->updateTotalAmount();
        });
    }
}
