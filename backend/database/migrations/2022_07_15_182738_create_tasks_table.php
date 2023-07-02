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
        Schema::create('tasks', function (Blueprint $table) {
			$table->id();
			$table->foreignId('account_id');
			$table->foreignId('user_id');
			$table->string('contact_id')->nullable();
			$table->foreignId('company_id')->nullable();
			$table->foreignId('goal_id')->nullable();
            $table->foreignId('project_id')->nullable();
			$table->foreignId('stage_id')->nullable();
			$table->string('name');
			$table->string('department')->nullable();
            $table->string('type')->nullable();
			$table->decimal('points', 4,1)->nullable();
			$table->longText('description')->nullable();
			$table->date('date_due')->nullable();
			$table->date('date_start')->nullable();
			$table->date('date_conclusion')->nullable();
			$table->string('status')->nullable();
			$table->string('priority')->nullable();
			$table->tinyInteger('trash')->nullable();
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
        Schema::dropIfExists('tasks');
    }
};
