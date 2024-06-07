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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('type')->after('name')->nullable();
            $table->decimal('budget', 15, 2)->after('goal_id')->nullable();
            $table->decimal('actual_cost', 15, 2)->before('status')->nullable();
            $table->string('objective')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'budget',
                'actual_cost',
                'objective'
            ]);
        });
    }
};