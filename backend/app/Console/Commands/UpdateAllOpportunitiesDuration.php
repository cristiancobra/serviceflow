<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Opportunity;

class UpdateAllOpportunitiesDuration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opportunities:updateAllDuration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all opportunities duration time.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Opportunity::all()->each->updateDuration();

        $this->info('Opportunity duration updated successfully.');
    
        return 0;
    }
}
