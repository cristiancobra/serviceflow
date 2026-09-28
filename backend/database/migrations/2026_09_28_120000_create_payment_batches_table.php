<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Um "lote de pagamento" representa uma única movimentação no extrato bancário
     * que quita várias invoices de uma vez (ex: um PIX de R$ 4.000 pagando 4 contas).
     * Cada invoice continua recebendo sua própria transaction, e as transactions do
     * mesmo lote apontam para ele via payment_batch_id — assim o saldo das invoices
     * não muda de lógica e a listagem de movimentações pode agrupá-las em uma linha.
     *
     * Transactions antigas ficam com payment_batch_id nulo (= pagamento individual).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->dateTime('transaction_date');
            $table->string('type');
            $table->string('method');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('payment_batch_id')->nullable()->after('credit_card_charge_id')
                ->constrained('payment_batches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_batch_id']);
            $table->dropColumn('payment_batch_id');
        });

        Schema::dropIfExists('payment_batches');
    }
};
