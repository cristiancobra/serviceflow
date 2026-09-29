<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chave Pix de fornecedores (empresa ou pessoa), usada no QR Code das contas a pagar.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('pix_key', 77)->nullable()->after('cnpj');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->string('pix_key', 77)->nullable()->after('cel_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('pix_key');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('pix_key');
        });
    }
};
