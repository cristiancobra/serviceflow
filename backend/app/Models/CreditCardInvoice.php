<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToAccount;

class CreditCardInvoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToAccount;

    // Constantes para os status da fatura do cartão
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';

    protected $fillable = [
        'account_id',
        'credit_card_id',
        'reference_month',
        'reference_year',
        'closing_date',
        'due_date',
        'total_amount',
        'total_paid',
        'balance',
        'status',
    ];

    protected $casts = [
        'closing_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function creditCard()
    {
        return $this->belongsTo(CreditCard::class);
    }

    public function charges()
    {
        return $this->hasMany(CreditCardCharge::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)
            ->whereNull('deleted_at')
            ->orderBy('transaction_date', 'asc');
    }

    /**
     * Recalcula total_amount a partir dos lançamentos (compras) da fatura.
     */
    public function updateTotalAmount()
    {
        $totalAmount = $this->charges()->sum('amount');
        $this->update(['total_amount' => $totalAmount, 'balance' => $totalAmount - $this->total_paid]);

        return $totalAmount;
    }

    /**
     * Recalcula total_paid e balance a partir dos pagamentos (transactions) da fatura.
     */
    public function updateTotalPaid()
    {
        $totalPaid = $this->transactions()->where('type', 'debit')->sum('amount') -
                     $this->transactions()->where('type', 'credit')->sum('amount');

        $balance = $this->total_amount - $totalPaid;
        $this->update(['total_paid' => $totalPaid, 'balance' => $balance]);
        $this->updateStatus();

        return $totalPaid;
    }

    /**
     * Calcula o status da fatura com base no pagamento e na data de vencimento.
     * Faturas ainda 'open' (aceitando lançamentos) não são recalculadas aqui —
     * o fechamento (open -> closed) é feito à parte, pelo fechamento do ciclo.
     */
    public function calculateStatus()
    {
        if ($this->status === self::STATUS_OPEN) {
            return self::STATUS_OPEN;
        }

        $totalPaid = $this->total_paid ?? 0;
        $totalAmount = $this->total_amount ?? 0;

        if ($totalAmount > 0 && $totalPaid >= $totalAmount) {
            return self::STATUS_PAID;
        }

        if (now()->greaterThan($this->due_date)) {
            return self::STATUS_OVERDUE;
        }

        if ($totalPaid > 0) {
            return self::STATUS_PARTIAL;
        }

        return self::STATUS_CLOSED;
    }

    public function updateStatus()
    {
        $newStatus = $this->calculateStatus();

        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
        }

        return $newStatus;
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_OPEN => 'Em Aberto',
            self::STATUS_CLOSED => 'Fechada',
            self::STATUS_PARTIAL => 'Parcialmente Paga',
            self::STATUS_PAID => 'Paga',
            self::STATUS_OVERDUE => 'Vencida',
        ];
    }

    public function getStatusLabelAttribute()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? 'Desconhecido';
    }

    public function getReferenceLabelAttribute()
    {
        $months = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ];

        return ($months[$this->reference_month] ?? $this->reference_month) . '/' . $this->reference_year;
    }
}
