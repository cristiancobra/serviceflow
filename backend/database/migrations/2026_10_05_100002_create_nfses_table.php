<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Notas fiscais de serviço (NFS-e Nacional) emitidas a partir das faturas.
     * Uma fatura pode ter mais de uma nota (ex: uma cancelada e a que a substituiu),
     * por isso o histórico fica numa tabela própria e não em colunas de invoices.
     *
     * Sem soft delete: documento fiscal não é apagado, é cancelado.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('invoice_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained();

            $table->string('environment', 20);
            $table->unsignedInteger('dps_series');
            $table->unsignedBigInteger('dps_number');
            $table->string('status', 20)->default('pending');

            // Dados enviados na DPS (guardados para conferência, mesmo que o cadastro mude depois)
            $table->char('service_code', 6);
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->date('competence_date');

            // Retorno da Sefin Nacional
            $table->string('access_key', 50)->nullable()->unique();
            $table->string('nfse_number', 20)->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->text('error_message')->nullable();

            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();

            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();

            $table->timestamps();

            $table->unique(['account_id', 'environment', 'dps_series', 'dps_number'], 'nfses_dps_unique');
            $table->index(['invoice_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfses');
    }
};
