<?php

namespace App\Console\Commands;

use App\Models\ApiCriteria;
use App\Models\ApiLog;
use Illuminate\Console\Command;

class ApiCheckEndpointCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:check-endpoint {path?} {method?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if an API endpoint is configured in the API Switcher';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');
        $method = $this->argument('method');
        
        if (!$path) {
            $path = $this->ask('Enter the API path (without /api/ prefix)');
        }
        
        if (!$method) {
            $method = $this->choice('Select HTTP method', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'], 0);
        }
        
        $this->info("Checking endpoint: {$method} /{$path}");
        
        $criteria = ApiCriteria::findMatchingCriteria($path, $method);
        
        if (!$criteria) {
            $this->warn('No matching API criteria found.');
            $this->info('Default behavior will be used: ' . config('api_switcher.default_behavior', 'pass'));
            return;
        }
        
        $this->info('Found matching criteria:');
        $this->table(
            ['ID', 'Name', 'Type', 'Status Code', 'Active'],
            [[
                $criteria->id,
                $criteria->name,
                $criteria->type,
                $criteria->status_code,
                $criteria->is_active ? 'Yes' : 'No'
            ]]
        );
        
        // Show recent logs for this endpoint
        $this->info("\nRecent logs for this endpoint:");
        $logs = ApiLog::where('path', $path)
                    ->where('method', $method)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                    
        if ($logs->isEmpty()) {
            $this->info('No recent logs found for this endpoint.');
        } else {
            $this->table(
                ['Time', 'Type', 'Status', 'Response Type'],
                $logs->map(function($log) {
                    return [
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->type,
                        $log->status_code,
                        $log->response_type
                    ];
                })->toArray()
            );
        }
    }
}
