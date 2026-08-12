<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('proposal_id')->nullable()->change();
        });
    }

    public function down()
    {
        // Apenas torna nullable de volta para required se não houver registros sem proposal_id
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('proposal_id')->nullable(false)->change();
        });
    }
};
