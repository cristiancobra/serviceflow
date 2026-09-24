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
     * Liga uma transaction (pagamento de uma invoice qualquer) à compra no cartão
     * de crédito (CreditCardCharge) gerada quando o método de pagamento é "cartão
     * de crédito" — diferente de credit_card_invoice_id, que representa o pagamento
     * da própria fatura do cartão a partir de uma conta bancária.
     *
     * Nesse caso não existe conta bancária nenhuma envolvida (o valor vira dívida
     * no cartão, não uma movimentação de conta), então bank_account_id passa a ser
     * nullable — até então era sempre obrigatório.
     *
     * @return void
     */
    public function up()
    {
        $isMysql = DB::connection()->getDriverName() === 'mysql';

        if ($isMysql) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['bank_account_id']);
            });
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_account_id')->nullable()->change();
        });

        Schema::table('transactions', function (Blueprint $table) use ($isMysql) {
            if ($isMysql) {
                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade');
            }
            $table->foreignId('credit_card_charge_id')->nullable()->after('credit_card_invoice_id')
                ->constrained('credit_card_charges')->nullOnDelete();
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

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['credit_card_charge_id']);
            $table->dropColumn('credit_card_charge_id');
        });

        if ($isMysql) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['bank_account_id']);
            });
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_account_id')->nullable(false)->change();
        });

        if ($isMysql) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade');
            });
        }
    }
};
