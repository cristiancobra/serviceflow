<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToAccount;

class RecurringExpense extends Model
{
    use HasFactory, SoftDeletes, BelongsToAccount;

    const CATEGORY_FIXED = 'fixed';
    const CATEGORY_VARIABLE = 'variable';

    protected $fillable = [
        'account_id',
        'user_id',
        'department_id',
        'lead_id',
        'company_id',
        'name',
        'description',
        'category',
        'amount',
        'due_day',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getCategoryOptions()
    {
        return [
            self::CATEGORY_FIXED => 'Fixa',
            self::CATEGORY_VARIABLE => 'Variável',
        ];
    }

    /**
     * Data de vencimento (due_day) dentro do mês de $referenceMonth, ajustada para
     * o último dia do mês quando due_day não existir nele (ex: dia 31 em fevereiro).
     */
    public function dueDateFor(Carbon $referenceMonth): Carbon
    {
        $date = $referenceMonth->copy()->startOfMonth();

        return $date->day(min($this->due_day, $date->daysInMonth));
    }

    /**
     * Datas de vencimento dentro de [$rangeStart, $rangeEnd] em que esta despesa recorrente
     * deveria ter uma fatura gerada, respeitando também start_date/end_date da despesa.
     * Usado tanto pela geração diária (janela de 30 dias a partir de hoje) quanto pelo
     * backfill retroativo (de start_date até hoje ou uma data informada).
     *
     * @return Carbon[]
     */
    public function dueDatesBetween(Carbon $rangeStart, Carbon $rangeEnd): array
    {
        $start = $this->start_date->greaterThan($rangeStart) ? $this->start_date->copy() : $rangeStart->copy();
        $end = ($this->end_date && $this->end_date->lessThan($rangeEnd)) ? $this->end_date->copy() : $rangeEnd->copy();

        $dates = [];
        $cursor = $start->copy()->startOfMonth();

        while (true) {
            $due = $this->dueDateFor($cursor);

            if ($due->greaterThan($end)) {
                break;
            }

            if ($due->greaterThanOrEqualTo($start)) {
                $dates[] = $due->copy();
            }

            $cursor->addMonthNoOverflow();
        }

        return $dates;
    }

    /**
     * Cria (ou recupera, se já existir) a fatura de débito referente a esta despesa
     * recorrente para a data de vencimento informada. Idempotente: reexecutar para a
     * mesma data nunca duplica.
     */
    public function generateInvoiceForDate(Carbon $dueDate): Invoice
    {
        return Invoice::firstOrCreate(
            [
                'recurring_expense_id' => $this->id,
                'date_due' => $dueDate->toDateString(),
            ],
            [
                'account_id' => $this->account_id,
                'user_id' => $this->user_id,
                'department_id' => $this->department_id,
                'lead_id' => $this->lead_id,
                'company_id' => $this->company_id,
                'name' => $this->name,
                'price' => $this->amount,
                'balance' => $this->amount,
                'type' => 'debit',
                'category' => $this->category,
                'observations' => $this->description,
                'status' => Invoice::STATUS_PENDING,
                'installment_number' => 1,
                'installment_quantity' => 1,
            ]
        );
    }
}
