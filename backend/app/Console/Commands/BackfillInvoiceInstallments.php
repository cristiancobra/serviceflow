<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class BackfillInvoiceInstallments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:backfill-installments {--dry-run : Apenas mostra o que seria alterado, sem salvar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preenche installment_number e installment_quantity das faturas criadas antes desses campos existirem';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Recalcula para TODAS as faturas (não só as "sem valor"): a coluna tem default 1,
        // então uma fatura já numerada corretamente e uma nunca processada são indistinguíveis
        // no banco. Reprocessar é seguro e idempotente — o agrupamento reflete a leva real de
        // parcelas independentemente do valor atual salvo.
        $invoices = Invoice::all();

        if ($invoices->isEmpty()) {
            $this->info('Nenhuma fatura encontrada.');
            return Command::SUCCESS;
        }

        // Agrupa faturas que foram criadas juntas como parcelas da mesma "leva":
        // mesma conta, mesmo tipo (credit/debit), mesma proposta (ou nenhuma), mesmo nome
        // e mesmo instante de criação (todas as parcelas de um lote são criadas no mesmo request).
        $groups = $invoices->groupBy(function (Invoice $invoice) {
            return implode('|', [
                $invoice->account_id,
                $invoice->type,
                $invoice->proposal_id ?? 'none',
                $invoice->name ?? '',
                optional($invoice->created_at)->format('Y-m-d H:i:s'),
            ]);
        });

        $updated = 0;

        foreach ($groups as $group) {
            $ordered = $group->sortBy('date_due')->values();
            $quantity = $ordered->count();

            foreach ($ordered as $index => $invoice) {
                $number = $index + 1;

                if ($invoice->installment_number != $number || $invoice->installment_quantity != $quantity) {
                    $this->line("Invoice ID {$invoice->id}: {$invoice->installment_number}/{$invoice->installment_quantity} -> {$number}/{$quantity}");
                }

                if (!$dryRun) {
                    $invoice->installment_number = $number;
                    $invoice->installment_quantity = $quantity;
                    $invoice->saveQuietly();
                }

                $updated++;
            }
        }

        $this->info(($dryRun ? '[dry-run] ' : '') . "Backfill concluído! {$updated} faturas processadas em " . $groups->count() . ' lotes.');

        return Command::SUCCESS;
    }
}
