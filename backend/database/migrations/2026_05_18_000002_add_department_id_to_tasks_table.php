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
        Schema::table('tasks', function (Blueprint $table) {
            // Adiciona a coluna department_id
            $table->foreignId('department_id')->nullable()->after('stage_id')->constrained('departments')->onDelete('set null');
            
            // Remove a coluna antiga department (string) se ainda existir
            if (Schema::hasColumn('tasks', 'department')) {
                $table->dropColumn('department');
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
        Schema::table('tasks', function (Blueprint $table) {
            // Remove a foreign key e a coluna
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            
            // Recriar a coluna antiga se necessário
            $table->string('department')->nullable();
        });
    }
};
