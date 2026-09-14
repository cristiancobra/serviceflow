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
     * Datas de vencimento dentro da janela [$today, $today + $days] em que esta despesa
     * recorrente deveria ter uma fatura gerada, respeitando start_date/end_date.
     *
     * @return Carbon[]
     */
    public function dueDatesInWindow(Carbon $today, int $days): array
    {
        $windowEnd = $today->copy()->addDays($days);
        $dates = [];

        $cursor = $today->copy()->startOfMonth();

        while (true) {
            $due = $this->dueDateFor($cursor);

            if ($due->greaterThan($windowEnd)) {
                break;
            }

            if (
                $due->greaterThanOrEqualTo($today)
                && $due->greaterThanOrEqualTo($this->start_date)
                && (!$this->end_date || $due->lessThanOrEqualTo($this->end_date))
            ) {
                $dates[] = $due->copy();
            }

            $cursor->addMonthNoOverflow();
        }

        return $dates;
    }
}
