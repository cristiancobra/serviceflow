<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Task;
use App\Models\Concerns\BelongsToAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToAccount;

    // Constantes para os status da fatura
    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'account_id',
        'proposal_id',
        'name',
        'user_id',
        'lead_id',
        'company_id',
        'department_id',
        'recurring_expense_id',
        'date_due',
        'price',
        'total_paid',
        'balance',
        'status',
        'fiscal_invoice_number',
        'type',
        'category',
        'observations',
        'installment_number',
        'installment_quantity',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function nfses()
    {
        return $this->hasMany(Nfse::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)
            ->whereNull('deleted_at')
            ->orderBy('transaction_date', 'asc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Nome padrão da tarefa financeira: "Pagar: ..." para débito, "Receber: ..."
     * para crédito, com "(n/q)" quando é parcela.
     */
    public function financialTaskName(): string
    {
        $label = $this->name
            ?? optional(optional($this->proposal)->opportunity)->name
            ?? ('Fatura #' . $this->id);

        if ($this->installment_quantity > 1) {
            $label .= " ({$this->installment_number}/{$this->installment_quantity})";
        }

        return ($this->type === 'credit' ? 'Receber: ' : 'Pagar: ') . $label;
    }

    /**
     * Prazo padrão da tarefa financeira em UTC: fim do dia de vencimento no fuso do
     * responsável. date_due da fatura é só data; gravá-la crua (00:00 UTC) faria a
     * tarefa aparecer às 21:00 do dia anterior em São Paulo.
     */
    public function financialTaskDueDate(?string $timezone = null): Carbon
    {
        $timezone = $timezone ?? optional($this->user)->timezone ?? 'America/Sao_Paulo';

        return Carbon::parse(Carbon::parse($this->date_due)->toDateString() . ' 23:59:00', $timezone)
            ->utc();
    }

    /**
     * Atributos padrão da tarefa financeira ligada a esta fatura.
     * Usa account_id/user_id da própria fatura para funcionar também sem
     * usuário logado (ex: comando agendado).
     */
    public function financialTaskAttributes($departmentId = null): array
    {
        return [
            'account_id'     => $this->account_id,
            'user_id'        => $this->user_id,
            'invoice_id'     => $this->id,
            'opportunity_id' => optional($this->proposal)->opportunity_id,
            'department_id'  => $departmentId,
            'name'           => $this->financialTaskName(),
            'date_due'       => $this->financialTaskDueDate()->toDateTimeString(),
            'status'         => 'to-do',
            'priority'       => 'medium',
        ];
    }

    /**
     * Cria a tarefa financeira ligada à fatura com os atributos padrão.
     * Falha na tarefa não impede a criação da fatura.
     */
    public function createFinancialTask($departmentId = null): bool
    {
        try {
            Task::create($this->financialTaskAttributes($departmentId));

            return true;
        } catch (\Exception $taskException) {
            Log::error('Erro ao criar tarefa para invoice', [
                'invoice_id' => $this->id,
                'error'      => $taskException->getMessage(),
            ]);

            return false;
        }
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function recurringExpense()
    {
        return $this->belongsTo(RecurringExpense::class);
    }

    /**
     * Recalculate and update the total_paid and balance based on transactions
     */
    public function updateTotalPaid()
    {
        // Para faturas de CRÉDITO (recebíveis): soma créditos - débitos
        // Para faturas de DÉBITO (pagáveis): soma débitos - créditos
        if ($this->type === 'debit') {
            // Fatura de despesa: pagamentos são débitos
            $totalPaid = $this->transactions()->where('type', 'debit')->sum('amount') -
                         $this->transactions()->where('type', 'credit')->sum('amount');
        } else {
            // Fatura de receita: recebimentos são créditos
            $totalPaid = $this->transactions()->where('type', 'credit')->sum('amount') -
                         $this->transactions()->where('type', 'debit')->sum('amount');
        }
        
        $balance = $this->price - $totalPaid;
        $this->update(['total_paid' => $totalPaid, 'balance' => $balance]);
        
        // Atualiza o status de pagamento da proposta
        if ($this->proposal) {
            $this->proposal->updatePaymentStatus();
        }
        
        return $totalPaid;
    }

    /**
     * Calculate the invoice status based on payment and due date
     */
    public function calculateStatus()
    {
        // Cancelada é um estado definido manualmente, não recalculado por pagamento/data
        if ($this->status === self::STATUS_CANCELLED) {
            return self::STATUS_CANCELLED;
        }

        $totalPaid = $this->total_paid ?? 0;
        $price = $this->price ?? 0;

        // Se está totalmente pago
        if ($price > 0 && $totalPaid >= $price) {
            return self::STATUS_PAID;
        }

        // Se está vencida (tem ou não pagamento parcial)
        if (now()->greaterThan($this->date_due)) {
            return self::STATUS_OVERDUE;
        }

        // Pagamento parcial dentro do prazo
        if ($totalPaid > 0) {
            return self::STATUS_PARTIAL;
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

    /**
     * Divide um valor total em $count parcelas de 2 casas decimais cuja soma bate
     * exatamente com o valor original, jogando o resto do arredondamento na última parcela.
     *
     * Única fonte de verdade para esse cálculo — usada tanto na redistribuição de parcelas
     * (Proposal::redistributeInvoices) quanto no ajuste manual de uma fatura já criada
     * (InvoiceController::adjustInvoicePrices).
     *
     * @return float[] Lista com $count valores, na ordem em que devem ser aplicados às parcelas.
     */
    public static function splitIntoInstallments(float $totalAmount, int $count)
    {
        if ($count <= 0) {
            return [];
        }

        $perInstallment = floor(($totalAmount * 100) / $count) / 100;
        $amounts = array_fill(0, $count, $perInstallment);
        $amounts[$count - 1] = round($totalAmount - ($perInstallment * ($count - 1)), 2);

        return $amounts;
    }
}
