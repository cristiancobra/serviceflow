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
        Schema::table('recurring_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id')->nullable()->after('department_id');
            $table->unsignedBigInteger('company_id')->nullable()->after('lead_id');

            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recurring_expenses', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropForeign(['company_id']);
            $table->dropColumn(['lead_id', 'company_id']);
        });
    }
};
