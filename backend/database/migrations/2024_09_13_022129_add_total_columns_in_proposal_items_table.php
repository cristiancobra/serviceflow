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
        Schema::table('proposal_items', function (Blueprint $table) {
            $table->decimal('labor_hours_total', 8, 1)->after('quantity');
            $table->decimal('total_profit', 8, 2)->after('quantity');
            $table->decimal('total_price', 8, 2)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proposal_items', function (Blueprint $table) {
            $table->dropColumn('labor_hours_total');
            $table->dropColumn('total_profit');
            $table->dropColumn('total_price');
        });
    }
};
