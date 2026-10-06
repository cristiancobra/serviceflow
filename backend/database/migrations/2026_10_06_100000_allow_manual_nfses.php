<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite registrar na fatura uma nota emitida fora do sistema (ex: direto no Emissor Nacional).
     * Essas notas não passam por DPS, então os campos que só servem à emissão automática viram opcionais.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfses', function (Blueprint $table) {
            $table->boolean('is_manual')->default(false)->after('user_id');
            $table->string('environment', 20)->nullable()->change();
            $table->unsignedInteger('dps_series')->nullable()->change();
            $table->unsignedBigInteger('dps_number')->nullable()->change();
            $table->char('service_code', 6)->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nfses', function (Blueprint $table) {
            $table->dropColumn('is_manual');
            $table->string('environment', 20)->nullable(false)->change();
            $table->unsignedInteger('dps_series')->nullable(false)->change();
            $table->unsignedBigInteger('dps_number')->nullable(false)->change();
            $table->char('service_code', 6)->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
};
