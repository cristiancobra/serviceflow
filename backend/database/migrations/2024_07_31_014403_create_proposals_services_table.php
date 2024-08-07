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
        Schema::create('proposals_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->string('name');
            $table->integer('quantity');
            $table->string('category')->nullable();
            $table->decimal('labor_hours', 8, 1);
            $table->decimal('labor_hourly_rate', 8, 2);
            $table->decimal('labor_hourly_rate_total', 8, 2);
            $table->decimal('profit_percentage', 5, 2);
            $table->decimal('profit', 8, 2);
            $table->decimal('price', 8, 2);
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proposals_services');
    }
};
