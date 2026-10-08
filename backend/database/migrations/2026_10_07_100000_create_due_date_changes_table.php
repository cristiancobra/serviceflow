<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Histórico de alterações do prazo (date_due) de tarefas e oportunidades.
     * O date_due da entidade continua sendo o prazo atual; cada mudança fica
     * registrada aqui com o motivo, para saber o prazo original, quantas vezes
     * foi adiado e por quê (repriorização, cliente não respondeu etc.).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('due_date_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->morphs('changeable');
            $table->foreignId('user_id')->nullable()->constrained();

            $table->dateTime('previous_date')->nullable();
            $table->dateTime('new_date')->nullable();
            $table->string('reason', 40)->nullable();
            $table->string('note', 500)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('due_date_changes');
    }
};
