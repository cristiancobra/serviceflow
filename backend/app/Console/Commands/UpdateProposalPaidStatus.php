<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proposal;

class UpdateProposalPaidStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proposals:update-paid-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o campo paid_at de todas as propostas baseado no status de pagamento das faturas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando atualização do status de pagamento das propostas...');

        // Busca todas as propostas com suas invoices de crédito
        $proposals = Proposal::with(['invoices' => function($query) {
            $query->whereNull('deleted_at')
                  ->where('type', 'credit');
        }])->get();

        $updatedCount = 0;
        $alreadyPaidCount = 0;

        $this->info("Total de propostas a processar: {$proposals->count()}");

        $this->withProgressBar($proposals, function ($proposal) use (&$updatedCount, &$alreadyPaidCount) {
            $hadPaidAt = !is_null($proposal->paid_at);
            
            // Chama o método que já implementamos
            $proposal->updatePaymentStatus();
            
            if ($proposal->paid_at && !$hadPaidAt) {
                $updatedCount++;
            } elseif ($proposal->paid_at) {
                $alreadyPaidCount++;
            }
        });

        $this->newLine(2);
        $this->info("✓ Concluído!");
        $this->line("  • {$updatedCount} propostas marcadas como pagas (novas)");
        $this->line("  • {$alreadyPaidCount} propostas já estavam marcadas como pagas");
        
        return Command::SUCCESS;
    }
}
