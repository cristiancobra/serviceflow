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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('color')->default('#6B7280'); // cor padrão cinza
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            // Index composto para garantir slug único por conta
            $table->unique(['account_id', 'slug']);
        });

        // Inserir os departamentos padrão para cada account
        $accounts = DB::table('accounts')->get();
        
        foreach ($accounts as $account) {
            $departments = [
                [
                    'account_id' => $account->id,
                    'name' => 'Operacional',
                    'slug' => 'operacional',
                    'color' => '#3B82F6', // azul
                    'icon' => 'fa-cogs',
                    'description' => 'Departamento responsável pelas operações e processos',
                    'order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'account_id' => $account->id,
                    'name' => 'Administrativo',
                    'slug' => 'administrativo',
                    'color' => '#8B5CF6', // roxo
                    'icon' => 'fa-briefcase',
                    'description' => 'Departamento administrativo',
                    'order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'account_id' => $account->id,
                    'name' => 'Financeiro',
                    'slug' => 'financeiro',
                    'color' => '#10B981', // verde
                    'icon' => 'fa-dollar-sign',
                    'description' => 'Departamento financeiro e contábil',
                    'order' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'account_id' => $account->id,
                    'name' => 'Vendas',
                    'slug' => 'vendas',
                    'color' => '#EF4444', // vermelho
                    'icon' => 'fa-chart-line',
                    'description' => 'Departamento de vendas e comercial',
                    'order' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'account_id' => $account->id,
                    'name' => 'Marketing',
                    'slug' => 'marketing',
                    'color' => '#F59E0B', // laranja
                    'icon' => 'fa-bullhorn',
                    'description' => 'Departamento de marketing e comunicação',
                    'order' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'account_id' => $account->id,
                    'name' => 'Recursos Humanos',
                    'slug' => 'recursos-humanos',
                    'color' => '#EC4899', // rosa
                    'icon' => 'fa-users',
                    'description' => 'Departamento de recursos humanos',
                    'order' => 6,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'account_id' => $account->id,
                    'name' => 'Tecnologia',
                    'slug' => 'tecnologia',
                    'color' => '#06B6D4', // ciano
                    'icon' => 'fa-laptop-code',
                    'description' => 'Departamento de tecnologia da informação',
                    'order' => 7,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'account_id' => $account->id,
                    'name' => 'Jurídico',
                    'slug' => 'juridico',
                    'color' => '#6366F1', // índigo
                    'icon' => 'fa-gavel',
                    'description' => 'Departamento jurídico',
                    'order' => 8,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'account_id' => $account->id,
                    'name' => 'Atendimento',
                    'slug' => 'atendimento',
                    'color' => '#14B8A6', // teal
                    'icon' => 'fa-headset',
                    'description' => 'Departamento de atendimento ao cliente',
                    'order' => 9,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];
            
            DB::table('departments')->insert($departments);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
};
