<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Configuração fiscal da conta para emissão de NFS-e Nacional (prestador).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('zip_code', 9)->nullable()->after('address_city');
            $table->char('ibge_city_code', 7)->nullable()->after('zip_code');
            $table->string('nfse_environment', 20)->default('restricted')->after('inscricao_municipal');
            $table->char('nfse_default_service_code', 6)->nullable()->after('nfse_environment');
            $table->unsignedInteger('nfse_dps_series')->default(1)->after('nfse_default_service_code');
            $table->unsignedBigInteger('nfse_next_dps_number')->default(1)->after('nfse_dps_series');
            $table->string('nfse_certificate_path')->nullable()->after('nfse_next_dps_number');
            $table->text('nfse_certificate_password')->nullable()->after('nfse_certificate_path');
            $table->string('nfse_certificate_holder')->nullable()->after('nfse_certificate_password');
            $table->dateTime('nfse_certificate_expires_at')->nullable()->after('nfse_certificate_holder');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn([
                'zip_code',
                'ibge_city_code',
                'nfse_environment',
                'nfse_default_service_code',
                'nfse_dps_series',
                'nfse_next_dps_number',
                'nfse_certificate_path',
                'nfse_certificate_password',
                'nfse_certificate_holder',
                'nfse_certificate_expires_at',
            ]);
        });
    }
};
