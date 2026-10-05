<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dados do tomador (empresa ou pessoa) e do serviço exigidos na DPS da NFS-e Nacional.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->char('ibge_city_code', 7)->nullable()->after('zip_code');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->char('cpf', 11)->nullable()->after('name');
            $table->char('ibge_city_code', 7)->nullable()->after('zip_code');
        });

        // cTribNac: código de tributação nacional (6 dígitos)
        Schema::table('services', function (Blueprint $table) {
            $table->char('nfse_service_code', 6)->nullable()->after('category');
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
            $table->dropColumn('ibge_city_code');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['cpf', 'ibge_city_code']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('nfse_service_code');
        });
    }
};
