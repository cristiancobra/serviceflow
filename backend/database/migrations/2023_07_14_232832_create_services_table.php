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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('name');
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
        Schema::dropIfExists('services');
    }
};
