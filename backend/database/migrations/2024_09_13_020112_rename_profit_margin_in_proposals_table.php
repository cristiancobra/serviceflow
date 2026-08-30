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
        if (Schema::hasColumn('proposals', 'profit_margin')) {
            Schema::table('proposals', function (Blueprint $table) {
                $table->renameColumn('profit_margin', 'total_profit');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('proposals', 'total_profit')) {
            Schema::table('proposals', function (Blueprint $table) {
                $table->renameColumn('total_profit', 'profit_margin');
            });
        }
    }
};
