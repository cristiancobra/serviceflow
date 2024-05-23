<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Journey;

class FixUtcDateTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journeys:fixUtc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converte as horas que estao com -3 horas para o padrão UTC';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Obtém todas as jornadas
        $journeys = Journey::all();

        // Itera sobre cada jornada e adiciona 6 horas às datas de início e fim
        foreach ($journeys as $journey) {
            // Adiciona 6 horas à data de início
            $journey->start = date('Y-m-d H:i:s', strtotime($journey->start) + 6 * 3600);
            
            // Adiciona 6 horas à data de fim, se não for nula
            if ($journey->end !== null) {
                $journey->end = date('Y-m-d H:i:s', strtotime($journey->end) + 6 * 3600);
            }

            // Salva as alterações
            $journey->save();
        }

        $this->info('Horas adicionadas às datas de início e fim das jornadas com sucesso.');

        return 0;
    }
}