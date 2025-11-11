<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class RecalculateInvoiceTotalPaid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:recalculate-total-paid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate total_paid for all invoices based on their transactions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting recalculation of total_paid for all invoices...');

        $invoices = Invoice::all();
        $count = 0;

        foreach ($invoices as $invoice) {
            $oldTotal = $invoice->total_paid;
            $newTotal = $invoice->updateTotalPaid();
            
            if ($oldTotal != $newTotal) {
                $this->line("Invoice ID {$invoice->id}: {$oldTotal} -> {$newTotal}");
            }
            
            $count++;
        }

        $this->info("Recalculation completed! {$count} invoices processed.");

        return Command::SUCCESS;
    }
}
