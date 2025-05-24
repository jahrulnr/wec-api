<?php

namespace App\Console\Commands;

use App\Models\ApiCriteria;
use Illuminate\Console\Command;

class ApiListCriteriaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:list-criteria';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all API switcher criteria';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $criteria = ApiCriteria::all();
        
        if ($criteria->isEmpty()) {
            $this->info('No API criteria found.');
            return;
        }
        
        $headers = ['ID', 'Name', 'Method', 'Path', 'Type', 'Status', 'Active'];
        $rows = [];
        
        foreach ($criteria as $c) {
            $rows[] = [
                $c->id,
                $c->name,
                $c->method,
                $c->path,
                $c->type,
                $c->status_code,
                $c->is_active ? 'Yes' : 'No'
            ];
        }
        
        $this->table($headers, $rows);
        
        $this->info("\nTotal criteria: " . $criteria->count());
    }
}
