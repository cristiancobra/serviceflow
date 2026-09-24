<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToAccount;

class Transaction extends Model
{
    use HasFactory, SoftDeletes, BelongsToAccount;
    
    protected $fillable = [
        'invoice_id',
        'credit_card_invoice_id',
        'credit_card_charge_id',
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

    public function creditCardInvoice()
    {
        return $this->belongsTo(CreditCardInvoice::class);
    }

    /**
     * Compra lançada no cartão de crédito quando este pagamento (de uma invoice
     * qualquer) foi feito com o método "cartão de crédito". Não confundir com
     * creditCardInvoice(): aqui a transaction paga uma invoice normal e a compra
     * no cartão é só um efeito colateral; lá a transaction paga a própria fatura
     * do cartão.
     */
    public function creditCardCharge()
    {
        return $this->belongsTo(CreditCardCharge::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    // Events
    protected static function booted()
    {
        $updateParents = function ($transaction) {
            if ($transaction->invoice_id) {
                $transaction->invoice->updateTotalPaid();
            }

            if ($transaction->credit_card_invoice_id) {
                $transaction->creditCardInvoice->updateTotalPaid();
            }
        };

        static::created($updateParents);
        static::updated($updateParents);
        static::deleted($updateParents);
    }
}
