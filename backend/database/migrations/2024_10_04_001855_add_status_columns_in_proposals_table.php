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
        Schema::table('proposals', function (Blueprint $table) {
            $table->timestamp('draft_at')->nullable()->after('total_price');
            $table->timestamp('submitted_at')->nullable()->after('draft_at');
            $table->timestamp('accepted_at')->nullable()->after('submitted_at');
            $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            $table->timestamp('canceled_at')->nullable()->after('rejected_at');
            $table->enum('status', [
                'canceled',
                'draft',
                'submitted',
                'accepted',
                'rejected'
            ])
                ->after('total_price')
                ->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn('draft_at');
            $table->dropColumn('submitted_at');
            $table->dropColumn('accepted_at');
            $table->dropColumn('rejected_at');
            $table->dropColumn('canceled_at');
            $table->dropColumn('status');
        });
    }
};
