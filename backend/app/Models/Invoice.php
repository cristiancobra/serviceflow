<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    // Constantes para os status da fatura
    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'proposal_id',
        'user_id',
        'date_due',
        'price',
        'total_paid',
        'balance',
        'status',
        'fiscal_invoice_number',
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
     * Recalculate and update the total_paid and balance based on transactions
     */
    public function updateTotalPaid()
    {
        $totalPaid = $this->transactions()->where('type', 'credit')->sum('amount') -
                     $this->transactions()->where('type', 'debit')->sum('amount');
        $balance = $this->price - $totalPaid;
        $this->update(['total_paid' => $totalPaid, 'balance' => $balance]);
        return $totalPaid;
    }

    /**
     * Calculate the invoice status based on payment and due date
     */
    public function calculateStatus()
    {
        $totalPaid = $this->total_paid ?? 0;
        $price = $this->price ?? 0;

        // Se está totalmente pago
        if ($totalPaid >= $price) {
            return self::STATUS_PAID;
        }

        // Se está vencida e não tem pagamento
        if (now()->greaterThan($this->date_due)) {
            return self::STATUS_OVERDUE;
        }

        // Caso padrão: pendente
        return self::STATUS_PENDING;
    }

    /**
     * Update the invoice status based on current payment state
     */
    public function updateStatus()
    {
        $newStatus = $this->calculateStatus();
        
        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
        }
        
        return $newStatus;
    }

    /**
     * Get all available status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_PARTIAL => 'Parcialmente Pago',
            self::STATUS_PAID => 'Pago',
            self::STATUS_OVERDUE => 'Vencido',
            self::STATUS_CANCELLED => 'Cancelado',
        ];
    }

    /**
     * Get the status label in Portuguese
     */
    public function getStatusLabelAttribute()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? 'Desconhecido';
    }
}
