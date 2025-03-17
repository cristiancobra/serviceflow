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
        Schema::table('service_costs', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->after('quantity');
            $table->decimal('total_price', 8, 2)->after('price');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_costs', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('total_price');
        });
    }
};
