<?php

namespace App\Console\Commands;

use App\Models\ApiCriteria;
use Illuminate\Console\Command;

class ApiListCriteria extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:list-criteria {--type=all : Filter by type (all, real, mock)} {--active=all : Filter by active status (all, yes, no)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all API criteria in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $active = $this->option('active');
        
        $query = ApiCriteria::query();
        
        // Apply type filter
        if ($type === 'real') {
            $query->where('type', 'real');
        } elseif ($type === 'mock') {
            $query->where('type', 'mock');
        }
        
        // Apply active filter
        if ($active === 'yes') {
            $query->where('is_active', true);
        } elseif ($active === 'no') {
            $query->where('is_active', false);
        }
        
        $criteria = $query->get();
        
        if ($criteria->isEmpty()) {
            $this->info('No API criteria found matching the filters.');
            return 0;
        }
        
        $headers = ['ID', 'Name', 'Path', 'Method', 'Type', 'Status Code', 'Active'];
        $rows = [];
        
        foreach ($criteria as $c) {
            $rows[] = [
                $c->id,
                $c->name,
                $c->path,
                $c->method,
                $c->type,
                $c->status_code,
                $c->is_active ? 'Yes' : 'No',
            ];
        }
        
        $this->table($headers, $rows);
        $this->info("Total: " . count($criteria) . " criteria");
        
        return 0;
    }
}
