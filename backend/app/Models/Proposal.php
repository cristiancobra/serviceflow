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
        $this->load('invoices.transactions');
        
        $currentInvoices = $this->invoices()->get();
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

        // Get user_id from the first existing invoice to maintain consistency
        $existingUserId = $currentInvoices->first()->user_id;

        // Identify invoices with transactions (paid)
        $paidInvoices = $currentInvoices->filter(function ($invoice) {
            return $invoice->transactions && $invoice->transactions->count() > 0;
        });

        // Identify invoices without transactions (unpaid)
        $unPaidInvoices = $currentInvoices->filter(function ($invoice) {
            return (!$invoice->transactions || $invoice->transactions->count() === 0) 
                && $invoice->balance > 0;
        });

        $paidInvoicesCount = $paidInvoices->count();

        // VALIDATION: If new quantity is less than paid invoices, return error
        if ($newInstallmentQuantity < $paidInvoicesCount) {
            return [
                'status' => 'error',
                'message' => "Não é possível reduzir para {$newInstallmentQuantity} parcelas. Existem {$paidInvoicesCount} faturas já pagas.",
            ];
        }

        // CASE 1: NO PAID INVOICES - Delete all and recreate
        if ($paidInvoicesCount === 0) {
            // Delete all unpaid invoices
            foreach ($unPaidInvoices as $invoice) {
                $invoice->delete();
            }

            // Get the first due date from deleted invoices for reference
            $firstDueDate = $currentInvoices->first()->date_due;

            // Create new invoices with equal distribution
            $pricePerInstallment = floor(($this->total_price * 100) / $newInstallmentQuantity) / 100;
            $remainder = $this->total_price - ($pricePerInstallment * $newInstallmentQuantity);

            for ($i = 0; $i < $newInstallmentQuantity; $i++) {
                $dueDate = date('Y-m-d', strtotime("+$i month", strtotime($firstDueDate)));
                
                // Last invoice gets the remainder
                $invoicePrice = ($i === $newInstallmentQuantity - 1) 
                    ? $pricePerInstallment + $remainder 
                    : $pricePerInstallment;
                
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

            return [
                'status' => 'success',
                'message' => "Faturas recriadas com sucesso. {$newInstallmentQuantity} parcelas criadas.",
                'data' => [
                    'unpaid_invoices_deleted' => $unPaidInvoices->count(),
                    'paid_invoices_kept' => 0,
                    'new_invoices_created' => $newInstallmentQuantity,
                ]
            ];
        }

        // CASE 2: HAS PAID INVOICES - Keep paid, delete unpaid, create new for remaining
        $paidBalance = $paidInvoices->sum('price');
        $remainingBalance = $this->total_price - $paidBalance;
        
        // Number of new invoices needed
        $newInvoicesNeeded = $newInstallmentQuantity - $paidInvoicesCount;

        // Delete unpaid invoices
        foreach ($unPaidInvoices as $invoice) {
            $invoice->delete();
        }

        // Create new invoices for remaining balance
        if ($remainingBalance > 0 && $newInvoicesNeeded > 0) {
            // Get the last due date from paid invoices to calculate next dates
            $lastDueDate = $paidInvoices->max('date_due');

            $pricePerNewInstallment = floor(($remainingBalance * 100) / $newInvoicesNeeded) / 100;
            $remainder = $remainingBalance - ($pricePerNewInstallment * $newInvoicesNeeded);

            for ($i = 1; $i <= $newInvoicesNeeded; $i++) {
                $dueDate = date('Y-m-d', strtotime("+$i month", strtotime($lastDueDate)));
                
                // Last new invoice gets the remainder
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
                'unpaid_invoices_deleted' => $unPaidInvoices->count(),
                'paid_invoices_kept' => $paidInvoicesCount,
                'new_invoices_created' => $newInvoicesNeeded,
                'remaining_balance' => $remainingBalance,
            ]
        ];
    }
}
