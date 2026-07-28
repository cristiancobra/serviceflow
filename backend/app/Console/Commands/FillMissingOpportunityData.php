<?php

namespace App\Console\Commands;

use App\Models\Opportunity;
use Illuminate\Console\Command;

class FillMissingOpportunityData extends Command
{
    protected $signature = 'opportunities:fill-missing-data';
    protected $description = 'Preenche company_id e lead_id em oportunidades que estão com ambos null';

    public function handle()
    {
        $this->info('Iniciando preenchimento de dados faltantes em oportunidades...');

        // Busca oportunidades onde AMBOS company_id e lead_id são null
        $opportunities = Opportunity::whereNull('company_id')
            ->whereNull('lead_id')
            ->get();

        if ($opportunities->isEmpty()) {
            $this->info('Nenhuma oportunidade encontrada com dados faltantes.');
            return;
        }

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($opportunities as $opportunity) {
            try {
                // Se a oportunidade tem apenas company_id null, pula
                // Se tem apenas lead_id null, pula
                // Só atualiza se AMBOS forem null e conseguir dados válidos

                // Tenta obter dados de outras oportunidades do mesmo usuário
                $similarOpportunity = Opportunity::where('user_id', $opportunity->user_id)
                    ->where('id', '!=', $opportunity->id)
                    ->whereNotNull('company_id')
                    ->whereNotNull('lead_id')
                    ->first();

                if ($similarOpportunity) {
                    $opportunity->company_id = $similarOpportunity->company_id;
                    $opportunity->lead_id = $similarOpportunity->lead_id;
                    $opportunity->save();
                    $this->info("Oportunidade #{$opportunity->id}: Preenchida a partir de oportunidade similar");
                    $updated++;
                } else {
                    $this->warn("Oportunidade #{$opportunity->id}: Nenhuma oportunidade similar encontrada");
                    $skipped++;
                }
            } catch (\Exception $e) {
                $this->error("Oportunidade #{$opportunity->id}: Erro ao atualizar - {$e->getMessage()}");
                $errors++;
            }
        }

        $this->line("\n========== RESULTADO ==========");
        $this->info("Oportunidades atualizadas: $updated");
        $this->warn("Oportunidades puladas: $skipped");
        $this->error("Oportunidades com erro: $errors");
        $this->line("================================\n");
    }
}
