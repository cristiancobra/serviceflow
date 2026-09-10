<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Uma transaction paga OU uma fatura de cliente (invoice_id) OU uma fatura de
     * cartão de crédito (credit_card_invoice_id) — nunca as duas. Por isso invoice_id
     * passa a ser nullable aqui.
     *
     * SQLite (usado nos testes) não suporta dropar/recriar foreign keys via ALTER TABLE
     * como o MySQL, então os passos de drop/recreate da FK de invoice_id só rodam no MySQL;
     * no SQLite a própria checagem de FK já vem desabilitada (DB_FOREIGN_KEYS=false).
     *
     * @return void
     */
    public function up()
    {
        $isMysql = DB::connection()->getDriverName() === 'mysql';

        if ($isMysql) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['invoice_id']);
            });
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->change();
        });

        Schema::table('transactions', function (Blueprint $table) use ($isMysql) {
            if ($isMysql) {
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            }
            $table->foreignId('credit_card_invoice_id')->nullable()->after('invoice_id')
                ->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $isMysql = DB::connection()->getDriverName() === 'mysql';

        Schema::table('transactions', function (Blueprint $table) use ($isMysql) {
            if ($isMysql) {
                $table->dropForeign(['credit_card_invoice_id']);
            }
            $table->dropColumn('credit_card_invoice_id');
        });

        if ($isMysql) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['invoice_id']);
            });
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable(false)->change();
        });

        if ($isMysql) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            });
        }
    }
};
