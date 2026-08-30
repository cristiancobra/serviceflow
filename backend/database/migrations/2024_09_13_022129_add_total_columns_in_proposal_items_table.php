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
        Schema::table('proposal_services', function (Blueprint $table) {
            if (!Schema::hasColumn('proposal_services', 'labor_hours_total')) {
                $table->decimal('labor_hours_total', 8, 1)->after('quantity');
            }
            if (!Schema::hasColumn('proposal_services', 'total_profit')) {
                $table->decimal('total_profit', 8, 2)->after('quantity');
            }
            if (!Schema::hasColumn('proposal_services', 'total_price')) {
                $table->decimal('total_price', 8, 2)->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proposal_services', function (Blueprint $table) {
            $table->dropColumn('labor_hours_total');
            $table->dropColumn('total_profit');
            $table->dropColumn('total_price');
        });
    }
};
