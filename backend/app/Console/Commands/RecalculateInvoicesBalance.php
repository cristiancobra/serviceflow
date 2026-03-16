<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class RecalculateInvoicesBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:recalculate-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula o total_paid e balance de todas as faturas baseado nas transações';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando recálculo de faturas...');
        
        $invoices = Invoice::all();
        $total = $invoices->count();
        
        $this->info("Total de faturas encontradas: {$total}");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        $updated = 0;
        
        foreach ($invoices as $invoice) {
            $oldBalance = $invoice->balance;
            $oldTotalPaid = $invoice->total_paid;
            
            $invoice->updateTotalPaid();
            
            if ($oldBalance != $invoice->balance || $oldTotalPaid != $invoice->total_paid) {
                $updated++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        
        $this->newLine();
        $this->info("Recálculo concluído!");
        $this->info("Faturas atualizadas: {$updated} de {$total}");
        
        return Command::SUCCESS;
    }
}
