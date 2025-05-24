<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApiClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:clear-logs {--days=0 : Clear logs older than X days (0 for all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear API Switcher logs from the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        if (!$this->confirm('Are you sure you want to clear API logs?', true)) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        try {
            // Assuming we store logs in a table called api_logs
            // If you're using a different storage mechanism, modify accordingly
            $query = DB::table('api_logs');
            
            if ($days > 0) {
                $date = now()->subDays($days);
                $query->where('created_at', '<', $date);
                $this->info("Clearing API logs older than {$days} days...");
            } else {
                $this->info("Clearing all API logs...");
            }
            
            $count = $query->count();
            $query->delete();
            
            $this->info("Successfully cleared {$count} log entries.");
            
            // Additionally, we might want to clear files used for API request logging
            // Assuming logs are stored in storage/logs/api/
            $logsPath = storage_path('logs/api');
            if (is_dir($logsPath)) {
                $files = glob($logsPath . '/*.log');
                foreach ($files as $file) {
                    if ($days > 0) {
                        $modTime = filemtime($file);
                        if (time() - $modTime >= $days * 24 * 60 * 60) {
                            unlink($file);
                        }
                    } else {
                        unlink($file);
                    }
                }
                $this->info("Log files cleared from {$logsPath}");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error clearing logs: {$e->getMessage()}");
            return 1;
        }
    }
}
