<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class UpdateInvoiceBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:update-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update balance column for all invoices';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $invoices = Invoice::all();

        foreach ($invoices as $invoice) {
            $balance = $invoice->price - ($invoice->total_paid ?? 0);
            $invoice->update(['balance' => $balance]);
        }

        $this->info('Balances updated for ' . $invoices->count() . ' invoices.');

        return Command::SUCCESS;
    }
}
