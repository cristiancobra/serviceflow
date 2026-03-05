<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            // Verifica se a foreign key existe antes de tentar remover
            $foreignKeys = DB::select(
                "SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = 'invoices' 
                AND CONSTRAINT_TYPE = 'FOREIGN KEY' 
                AND CONSTRAINT_NAME LIKE '%lead_id%'",
                [env('DB_DATABASE', 'serviceflow')]
            );

            if (!empty($foreignKeys)) {
                $table->dropForeign(['lead_id']);
            }
            
            // Remove a coluna se existir
            if (Schema::hasColumn('invoices', 'lead_id')) {
                $table->dropColumn('lead_id');
            }
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
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
        });
    }
};
