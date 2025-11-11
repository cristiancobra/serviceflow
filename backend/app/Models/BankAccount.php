<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'user_id',
        'account_name',
        'account_number',
        'bank_name',
        'agency',
        'initial_balance',
        'type',
        'is_active',
        'description',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Constantes para tipos de conta
    const TYPE_CHECKING = 'checking';
    const TYPE_SAVINGS = 'savings';
    const TYPE_INVESTMENT = 'investment';
    const TYPE_OTHER = 'other';

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Atualiza o saldo da conta baseado nas transações
     */
    public function updateBalance()
    {
        $balance = $this->transactions()->sum('amount');
        $this->update(['balance' => $balance]);
        return $balance;
    }

    /**
     * Get all available account types
     */
    public static function getTypeOptions()
    {
        return [
            self::TYPE_CHECKING => 'Conta Corrente',
            self::TYPE_SAVINGS => 'Conta Poupança',
            self::TYPE_INVESTMENT => 'Conta Investimento',
            self::TYPE_OTHER => 'Outro',
        ];
    }

    /**
     * Get the type label in Portuguese
     */
    public function getTypeLabelAttribute()
    {
        $options = self::getTypeOptions();
        return $options[$this->type] ?? 'Desconhecido';
    }
}
