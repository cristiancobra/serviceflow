<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
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
            foreach ($recurringExpense->dueDatesBetween($today, $today->copy()->addDays(30)) as $dueDate) {
                $invoice = $recurringExpense->generateInvoiceForDate($dueDate);

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
