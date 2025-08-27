<?php

namespace App\Console\Commands;

use App\Models\Cheesecake;
use Illuminate\Console\Command;

class UpdateExpiredCheesecakes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheesecake:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status of expired cheesecakes automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updatedCount = Cheesecake::updateExpiredStatus();
        
        $this->info("Updated {$updatedCount} expired cheesecakes.");
        
        return Command::SUCCESS;
    }
}
