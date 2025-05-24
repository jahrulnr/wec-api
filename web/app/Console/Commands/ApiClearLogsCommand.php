<?php

namespace App\Console\Commands;

use App\Models\ApiLog;
use Illuminate\Console\Command;

class ApiClearLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:clear-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all API switcher logs from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = ApiLog::clearAllLogs();
        
        $this->info("Successfully cleared {$count} API log entries.");
    }
}
