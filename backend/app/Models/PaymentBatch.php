<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToAccount;

/**
 * Uma única movimentação bancária que quita várias invoices de uma vez. As
 * transactions do lote (uma por invoice) é que efetivamente abatem o saldo de
 * cada invoice; o lote só as agrupa e guarda o total, para bater com o extrato.
 */
class PaymentBatch extends Model
{
    use HasFactory, SoftDeletes, BelongsToAccount;

    protected $fillable = [
        'account_id',
        'bank_account_id',
        'amount',
        'transaction_date',
        'type',
        'method',
        'description',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Recalcula o total a partir das transactions restantes. Se não sobrou
     * nenhuma (todas excluídas individualmente), o lote deixa de existir.
     */
    public function updateAmount()
    {
        $remaining = $this->transactions()->count();

        if ($remaining === 0) {
            $this->delete();
            return 0;
        }

        $amount = $this->transactions()->sum('amount');
        $this->update(['amount' => $amount]);

        return $amount;
    }
}
