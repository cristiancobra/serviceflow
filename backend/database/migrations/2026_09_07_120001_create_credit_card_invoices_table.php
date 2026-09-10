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
        Schema::create('credit_card_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('credit_card_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('reference_month');
            $table->unsignedSmallInteger('reference_year');
            $table->date('closing_date');
            $table->date('due_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('status')->default('open');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['credit_card_id', 'reference_month', 'reference_year'],
                'credit_card_invoices_card_month_year_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_card_invoices');
    }
};
