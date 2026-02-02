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
     * This method:
     * 1. Finds invoices without transactions (unpaid)
     * 2. Redistributes the unpaid balance across the new number of installments
     * 3. Preserves paid invoices as-is
     * 4. Ensures the sum of all invoices equals exactly the proposal total_price
     *
     * @param int $newInstallmentQuantity The new number of installments
     * @return array Result with status and message
     */
    public function redistributeInvoices($newInstallmentQuantity)
    {
        $this->load('invoices.transactions');
        
        $currentInvoices = $this->invoices()->get();
        $oldInstallmentQuantity = $currentInvoices->count();

        // If there are no invoices, nothing to redistribute
        if ($oldInstallmentQuantity === 0) {
            return [
                'status' => 'info',
                'message' => 'Nenhuma fatura para redistribuir',
            ];
        }

        // Get user_id from the first existing invoice to maintain consistency
        $existingUserId = $currentInvoices->first()->user_id;

        // Identify invoices without transactions (unpaid)
        $unPaidInvoices = $currentInvoices->filter(function ($invoice) {
            return (!$invoice->transactions || $invoice->transactions->count() === 0) 
                && $invoice->balance > 0;
        });

        // If the new quantity equals the current quantity, no changes needed
        if ($newInstallmentQuantity === $oldInstallmentQuantity) {
            return [
                'status' => 'info',
                'message' => 'Quantidade de parcelas não foi alterada',
            ];
        }

        // Delete unpaid invoices (invoices without transactions)
        foreach ($unPaidInvoices as $invoice) {
            $invoice->delete();
        }

        // Get paid invoices that will be kept
        $paidInvoices = $currentInvoices->filter(function ($invoice) {
            return $invoice->transactions && $invoice->transactions->count() > 0;
        });

        // Calculate the total amount already paid
        $paidBalance = $paidInvoices->sum('price');
        
        // Calculate remaining balance to be distributed
        $remainingBalance = $this->total_price - $paidBalance;

        // Distribute the remaining balance across new installments
        if ($remainingBalance > 0 && $newInstallmentQuantity > 0) {
            // Calculate base price per installment (rounded down to 2 decimals)
            $pricePerInstallment = floor(($remainingBalance * 100) / $newInstallmentQuantity) / 100;
            
            // Calculate the remainder that will go to the last invoice
            $remainder = $remainingBalance - ($pricePerInstallment * $newInstallmentQuantity);
            
            $firstDueDate = $this->invoices()->orderBy('date_due')->first()->date_due ?? now();

            // Create new invoices
            for ($i = 0; $i < $newInstallmentQuantity; $i++) {
                $dueDate = date('Y-m-d', strtotime("+$i month", strtotime($firstDueDate)));
                
                // Last invoice gets the remainder to ensure total equals proposal price
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
        }

        return [
            'status' => 'success',
            'message' => "Faturas redistribuídas com sucesso. {$newInstallmentQuantity} parcelas criadas.",
            'data' => [
                'unpaid_invoices_deleted' => $unPaidInvoices->count(),
                'paid_invoices_kept' => $paidInvoices->count(),
                'new_invoices_created' => $newInstallmentQuantity,
                'remaining_balance' => $remainingBalance,
            ]
        ];
    }
}
