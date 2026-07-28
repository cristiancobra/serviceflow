<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Proposal;
use Illuminate\Console\Command;

class FillMissingInvoiceData extends Command
{
    protected $signature = 'invoices:fill-missing-data';
    protected $description = 'Preenche company_id e lead_id em invoices que estão com AMBOS null, baseado na oportunidade';

    public function handle()
    {
        $this->info('Iniciando preenchimento de dados faltantes em invoices...');

        // Buscar invoices com AMBOS company_id E lead_id null, com proposal_id
        $invoices = Invoice::whereNull('company_id')
            ->whereNull('lead_id')
            ->whereNotNull('proposal_id')
            ->get();

        $updated = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($invoices as $invoice) {
            try {
                $proposal = Proposal::with('opportunity')->find($invoice->proposal_id);

                if (!$proposal || !$proposal->opportunity) {
                    $this->warn("Invoice #{$invoice->id}: Proposta ou oportunidade não encontrada");
                    $failed++;
                    continue;
                }

                // Só preenche se AMBOS os IDs existem na opportunity
                if (!$proposal->opportunity->company_id || !$proposal->opportunity->lead_id) {
                    $this->warn("Invoice #{$invoice->id}: Oportunidade sem company_id ou lead_id");
                    $skipped++;
                    continue;
                }

                // Preencher AMBOS
                $invoice->company_id = $proposal->opportunity->company_id;
                $invoice->lead_id = $proposal->opportunity->lead_id;
                $invoice->save();

                $updated++;
                $this->line("✓ Invoice #{$invoice->id} atualizada");
            } catch (\Exception $e) {
                $this->error("Invoice #{$invoice->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->info("\n========== RESULTADO ==========");
        $this->info("Invoices atualizadas: {$updated}");
        $this->info("Invoices puladas: {$skipped}");
        $this->info("Invoices com erro: {$failed}");
        $this->info("================================");
    }
}
