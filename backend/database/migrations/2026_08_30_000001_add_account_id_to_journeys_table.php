<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('journeys', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->after('id');
        });

        DB::table('journeys')->select('journeys.id', 'tasks.account_id')
            ->join('tasks', 'tasks.id', '=', 'journeys.task_id')
            ->orderBy('journeys.id')
            ->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('journeys')->where('id', $row->id)->update(['account_id' => $row->account_id]);
                }
            });

        Schema::table('journeys', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable(false)->change();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('journeys', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};
