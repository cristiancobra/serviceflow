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
        Schema::create('proposal_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->unsignedBigInteger('proposal_id');
            $table->foreign('proposal_id')->references('id')->on('proposals');
            $table->unsignedBigInteger('cost_id');
            $table->foreign('cost_id')->references('id')->on('costs');            
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 8, 2);    
            $table->decimal('total_price', 8, 2);
            $table->softDeletes();   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proposal_costs');
    }
};
