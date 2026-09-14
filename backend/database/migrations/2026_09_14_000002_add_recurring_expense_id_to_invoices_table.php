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
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('recurring_expense_id')->nullable()->after('department_id');

            $table->foreign('recurring_expense_id')->references('id')->on('recurring_expenses')->onDelete('set null');
            $table->unique(['recurring_expense_id', 'date_due'], 'invoices_recurring_expense_due_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique('invoices_recurring_expense_due_unique');
            $table->dropForeign(['recurring_expense_id']);
            $table->dropColumn('recurring_expense_id');
        });
    }
};
