<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Department;
use App\Models\Invoice;

class BackfillRecurringExpenseTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring-expenses:backfill-tasks {--dry-run : Apenas mostra o que seria criado, sem salvar}
                            {--include-overdue : Inclui faturas já vencidas (por padrão só vencimento de hoje em diante)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria a tarefa "Pagar: ..." para faturas de despesas recorrentes em aberto geradas antes de o comando diário criar tarefas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Só faturas em aberto: pagas/canceladas não precisam de tarefa. Vencidas ficam de fora
        // por padrão porque costumam ser histórico lançado pelo backfill e não marcado como pago.
        // withTrashed evita
        // recriar uma tarefa que o usuário já tinha excluído de propósito. Idempotente:
        // rodar de novo não cria nada para faturas que já têm tarefa.
        $invoices = Invoice::withoutGlobalScopes()
            ->whereNotNull('recurring_expense_id')
            ->whereNotIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_CANCELLED])
            ->whereDoesntHave('tasks', function ($query) {
                $query->withoutGlobalScopes()->withTrashed();
            })
            ->when(!$this->option('include-overdue'), function ($query) {
                $query->whereDate('date_due', '>=', now()->toDateString());
            })
            ->orderBy('date_due')
            ->get();

        if ($invoices->isEmpty()) {
            $this->info('Nenhuma fatura recorrente em aberto sem tarefa.');
            return Command::SUCCESS;
        }

        $created = 0;

        foreach ($invoices as $invoice) {
            $this->line("Fatura #{$invoice->id} - {$invoice->name} - vencimento {$invoice->date_due}");

            if (!$dryRun && $invoice->createFinancialTask(Department::financeiroIdFor($invoice->account_id))) {
                $created++;
            }
        }

        $this->info($dryRun
            ? "Dry run: {$invoices->count()} tarefa(s) seriam criadas."
            : "Total de tarefas criadas: {$created}");

        return Command::SUCCESS;
    }
}
