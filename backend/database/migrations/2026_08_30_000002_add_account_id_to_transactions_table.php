<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->after('id');
        });

        DB::table('transactions')->select('transactions.id', 'bank_accounts.account_id')
            ->join('bank_accounts', 'bank_accounts.id', '=', 'transactions.bank_account_id')
            ->orderBy('transactions.id')
            ->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('transactions')->where('id', $row->id)->update(['account_id' => $row->account_id]);
                }
            });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable(false)->change();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};
