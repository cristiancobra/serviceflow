<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class UpdateInvoicesStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o status de todas as invoices baseado no total_paid';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando atualização de status das invoices...');

        $invoices = Invoice::with('transactions')->get();
        $bar = $this->output->createProgressBar($invoices->count());
        $bar->start();

        $updated = 0;
        foreach ($invoices as $invoice) {
            // Atualiza o total_paid baseado nas transações
            $invoice->updateTotalPaid();
            
            // Atualiza o status
            $invoice->updateStatus();
            
            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Total de {$updated} invoices atualizadas com sucesso!");

        return Command::SUCCESS;
    }
}
