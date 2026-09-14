<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\RecurringExpense;

class GenerateRecurringExpenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring-expenses:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera faturas (invoices) de despesas recorrentes ativas com até 30 dias de antecedência';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
        $generated = 0;

        foreach (RecurringExpense::active()->get() as $recurringExpense) {
            foreach ($recurringExpense->dueDatesInWindow($today, 30) as $dueDate) {
                $invoice = Invoice::firstOrCreate(
                    [
                        'recurring_expense_id' => $recurringExpense->id,
                        'date_due' => $dueDate->toDateString(),
                    ],
                    [
                        'account_id' => $recurringExpense->account_id,
                        'user_id' => $recurringExpense->user_id,
                        'department_id' => $recurringExpense->department_id,
                        'name' => $recurringExpense->name,
                        'price' => $recurringExpense->amount,
                        'balance' => $recurringExpense->amount,
                        'type' => 'debit',
                        'category' => $recurringExpense->category,
                        'observations' => $recurringExpense->description,
                        'status' => Invoice::STATUS_PENDING,
                        'installment_number' => 1,
                        'installment_quantity' => 1,
                    ]
                );

                if ($invoice->wasRecentlyCreated) {
                    $generated++;
                    $this->info("Fatura gerada: {$recurringExpense->name} - vencimento {$dueDate->toDateString()}");
                }
            }
        }

        $this->info("Total de faturas geradas: {$generated}");

        return Command::SUCCESS;
    }
}
