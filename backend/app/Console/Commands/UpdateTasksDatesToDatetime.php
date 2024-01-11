<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class UpdateTasksDatesToDatetime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:task-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insere tempo padrão nas tarefas que possuem apenas data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Atualiza o campo date_due para 23:59:59 (se NÃO for nulo)
        Task::whereNotNull('date_due')
            ->update([
                'date_due' => DB::raw('DATE_ADD(date_due, INTERVAL 1 DAY) - INTERVAL 1 SECOND')
            ]);

        // Atualiza o campo date_start para 00:00:00 (se NÃO for nulo)
        Task::whereNotNull('date_start')
            ->update([
                'date_start' => DB::raw('date_start')
            ]);

        // Atualiza o campo date_conclusion para 12:00:00 (se NÃO for nulo)
        Task::whereNotNull('date_conclusion')
            ->update([
                'date_conclusion' => DB::raw('DATE_ADD(date_conclusion, INTERVAL 12 HOUR)')
            ]);

        $this->info('Atualização concluída.');
    }
}