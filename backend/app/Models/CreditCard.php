<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToAccount;

class CreditCard extends Model
{
    use HasFactory, SoftDeletes, BelongsToAccount;

    protected $fillable = [
        'account_id',
        'user_id',
        'default_bank_account_id',
        'name',
        'brand',
        'last_digits',
        'credit_limit',
        'closing_day',
        'due_day',
        'is_active',
        'description',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function defaultBankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'default_bank_account_id');
    }

    public function invoices()
    {
        return $this->hasMany(CreditCardInvoice::class);
    }

    public function charges()
    {
        return $this->hasMany(CreditCardCharge::class);
    }

    /**
     * Fatura (mês de competência) para a qual uma compra feita em $purchaseDate deve cair,
     * de acordo com o dia de fechamento do cartão. Compras após o fechamento do mês caem
     * na fatura do mês seguinte. Cria a fatura se ainda não existir.
     */
    public function invoiceForPurchaseDate(Carbon $purchaseDate): CreditCardInvoice
    {
        $referenceDate = $purchaseDate->copy();

        if ($purchaseDate->day > $this->closing_day) {
            $referenceDate->addMonthNoOverflow();
        }

        $referenceMonth = $referenceDate->month;
        $referenceYear = $referenceDate->year;

        return $this->invoices()->firstOrCreate(
            [
                'reference_month' => $referenceMonth,
                'reference_year' => $referenceYear,
            ],
            [
                'account_id' => $this->account_id,
                'closing_date' => $this->dayInMonth($referenceYear, $referenceMonth, $this->closing_day),
                'due_date' => $this->dueDateFor($referenceYear, $referenceMonth),
                'status' => CreditCardInvoice::STATUS_OPEN,
            ]
        );
    }

    /**
     * Vencimento da fatura: due_day dentro do mês de fechamento, ou no mês seguinte
     * quando due_day for igual ou anterior ao closing_day (cartões cujo vencimento
     * cai "no mês seguinte ao fechamento").
     */
    private function dueDateFor(int $referenceYear, int $referenceMonth): Carbon
    {
        $dueDate = $this->dayInMonth($referenceYear, $referenceMonth, $this->due_day);

        if ($this->due_day <= $this->closing_day) {
            $dueDate->addMonthNoOverflow();
        }

        return $dueDate;
    }

    /**
     * Monta uma data para $day dentro de $year/$month, ajustando para o último dia
     * do mês quando $day não existir nele (ex: dia 31 em fevereiro).
     */
    private function dayInMonth(int $year, int $month, int $day): Carbon
    {
        $date = Carbon::create($year, $month, 1);

        return $date->day(min($day, $date->daysInMonth));
    }
}
