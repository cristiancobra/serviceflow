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
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->string('agency', 50)->nullable()->after('bank_name');
            $table->decimal('initial_balance', 15, 2)->default(0)->after('account_number');
            $table->enum('type', ['checking', 'savings', 'investment', 'other'])->default('checking')->after('initial_balance');
            $table->boolean('is_active')->default(true)->after('type');
            $table->text('description')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn(['agency', 'initial_balance', 'type', 'is_active', 'description']);
        });
    }
};
