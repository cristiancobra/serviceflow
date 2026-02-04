<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Opportunity;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\ProposalCost;
use App\Models\ProposalService;

class Proposal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'account_id',
        'opportunity_id',
        'name',
        'description',
        'date',
        'total_hours',
        'total_operational_cost',
        'total_third_party_cost',
        'total_profit',
        'total_profit_percentage',
        'total_discount',
        'total_price',
        'installment_quantity',
        'validity_days',
        'status',
    ];

    // relationships

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function proposalServices()
    {
        return $this->hasMany(ProposalService::class);
    }

    public function proposalCosts()
    {
        return $this->hasMany(ProposalCost::class);
    }

    // methods

    public static function getTotalValue()
    {
        return self::where('status', 'accepted')
            ->sum('total_price');
    }

    public static function getAcceptedCount()
    {
        return self::where('status', 'accepted')->count();
    }

    /**
     * Recalculate and redistribute invoice amounts when installment_quantity changes.
     * 
     * Logic:
     * 1. If NO paid invoices exist: Delete all old invoices and create new ones with equal distribution
     * 2. If paid invoices exist:
     *    - Keep paid invoices intact
     *    - Delete unpaid invoices
     *    - Create new invoices for the remaining balance
     *    - Error if new quantity < paid invoices count
     * 3. Distribute remaining balance equally among new invoices
     * 4. Add any rounding remainder to the last invoice
     *
     * @param int $newInstallmentQuantity The new number of installments
     * @return array Result with status and message
     */
    public function redistributeInvoices($newInstallmentQuantity)
    {
        // Carrega apenas faturas NÃO DELETADAS
        $currentInvoices = $this->invoices()
            ->whereNull('deleted_at')
            ->get();
        
        $currentInvoiceCount = $currentInvoices->count();

        // If there are no invoices, nothing to redistribute
        if ($currentInvoiceCount === 0) {
            return [
                'status' => 'info',
                'message' => 'Nenhuma fatura para redistribuir',
            ];
        }

        // If the new quantity equals the current quantity, no changes needed
        if ($newInstallmentQuantity === $currentInvoiceCount) {
            return [
                'status' => 'info',
                'message' => 'Quantidade de parcelas não foi alterada',
            ];
        }

        // Get user_id and first due date from the first existing invoice
        $existingUserId = $currentInvoices->first()->user_id;
        $firstDueDate = $currentInvoices->first()->date_due;

        // Identify invoices with paid amount (paid/partially paid)
        $paidInvoices = $currentInvoices->filter(function ($invoice) {
            return (float)$invoice->total_paid > 0;
        });

        // Identify invoices without paid amount (unpaid)
        $unPaidInvoices = $currentInvoices->filter(function ($invoice) {
            return (float)$invoice->total_paid <= 0;
        });

        $paidInvoicesCount = $paidInvoices->count();
        $unPaidInvoicesCount = $unPaidInvoices->count();

        // VALIDATION: If new quantity is less than paid invoices, return error
        if ($newInstallmentQuantity < $paidInvoicesCount) {
            return [
                'status' => 'error',
                'message' => "Não é possível reduzir para {$newInstallmentQuantity} parcelas. Existem {$paidInvoicesCount} faturas com valores já pagos.",
            ];
        }

        // Calculate balance from paid invoices
        $paidBalance = $paidInvoices->sum('price');
        $remainingBalance = $this->total_price - $paidBalance;
        $newInvoicesNeeded = $newInstallmentQuantity - $paidInvoicesCount;

        // DELETE all unpaid invoices
        if ($unPaidInvoicesCount > 0) {
            $unPaidInvoiceIds = $unPaidInvoices->pluck('id')->toArray();
            Invoice::whereIn('id', $unPaidInvoiceIds)->delete();
        }

        // CREATE new invoices for remaining balance
        if ($remainingBalance > 0 && $newInvoicesNeeded > 0) {
            $lastDueDate = $paidInvoices->count() > 0 ? $paidInvoices->max('date_due') : $firstDueDate;

            $pricePerNewInstallment = floor(($remainingBalance * 100) / $newInvoicesNeeded) / 100;
            $remainder = $remainingBalance - ($pricePerNewInstallment * $newInvoicesNeeded);

            for ($i = 1; $i <= $newInvoicesNeeded; $i++) {
                $dueDate = date('Y-m-d', strtotime("+$i month", strtotime($lastDueDate)));
                
                $invoicePrice = ($i === $newInvoicesNeeded) 
                    ? $pricePerNewInstallment + $remainder 
                    : $pricePerNewInstallment;
                
                Invoice::create([
                    'proposal_id' => $this->id,
                    'user_id' => $existingUserId,
                    'price' => $invoicePrice,
                    'balance' => $invoicePrice,
                    'total_paid' => 0,
                    'date_due' => $dueDate,
                    'status' => Invoice::STATUS_PENDING,
                ]);
            }
        }

        return [
            'status' => 'success',
            'message' => "Faturas redistribuídas com sucesso para {$newInstallmentQuantity} parcelas.",
            'data' => [
                'unpaid_invoices_deleted' => $unPaidInvoicesCount,
                'paid_invoices_kept' => $paidInvoicesCount,
                'new_invoices_created' => $newInvoicesNeeded,
                'remaining_balance' => $remainingBalance,
            ]
        ];
    }
}
