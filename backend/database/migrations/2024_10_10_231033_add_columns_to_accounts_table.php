<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('cnpj')->after('slug')->nullable();
            $table->string('inscricao_municipal')->after('cnpj')->nullable();
            $table->string('email')->after('inscricao_municipal')->nullable();
            $table->string('phone')->after('email')->nullable();
            $table->string('address')->after('phone')->nullable();
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
            $table->dropColumn('cnpj');
            $table->dropColumn('inscricao_municipal');
            $table->dropColumn('email');
            $table->dropColumn('phone');
            $table->dropColumn('address');
        });
    }
};
