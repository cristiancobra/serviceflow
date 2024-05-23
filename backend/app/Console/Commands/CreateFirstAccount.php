<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\User;

class CreateFirstAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:first-account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the first account for the system.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $existingAccountsCount = Account::count();
        
        if ($existingAccountsCount > 0) {
            $this->info('Já existe pelo menos uma conta na tabela accounts. Nenhuma ação necessária.');
            return;
        }

        $account = new Account();
        $account->name = 'Cobra Sistemas';
        $account->slug = 'cobra-sistemas';
        $account->owner_id = 1;
        $account->save();

        $this->info('Primeira conta criada com sucesso!');
    
        // $user = new User();
        // $user->name = 'Nome do Usuário';
        // $user->email = 'usuario@example.com';
        // $user->password = bcrypt('123');
        // $user->account_id = $account->id;
        // $user->save();

        // $this->info('Primeiro usuário associado à primeira conta criado com sucesso!');
    }

}
