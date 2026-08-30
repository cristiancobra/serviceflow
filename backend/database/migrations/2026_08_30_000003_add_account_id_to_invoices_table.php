<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->after('id');
        });

        // Preenche account_id a partir da proposta, empresa, lead ou usuário
        // relacionados, nessa ordem de prioridade (o primeiro que existir).
        DB::table('invoices')
            ->select(
                'invoices.id',
                'proposals.account_id as proposal_account_id',
                'companies.account_id as company_account_id',
                'leads.account_id as lead_account_id',
                'users.account_id as user_account_id'
            )
            ->leftJoin('proposals', 'proposals.id', '=', 'invoices.proposal_id')
            ->leftJoin('companies', 'companies.id', '=', 'invoices.company_id')
            ->leftJoin('leads', 'leads.id', '=', 'invoices.lead_id')
            ->leftJoin('users', 'users.id', '=', 'invoices.user_id')
            ->orderBy('invoices.id')
            ->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    $accountId = $row->proposal_account_id
                        ?? $row->company_account_id
                        ?? $row->lead_account_id
                        ?? $row->user_account_id;

                    DB::table('invoices')->where('id', $row->id)->update(['account_id' => $accountId]);
                }
            });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable(false)->change();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};
