<?php

namespace App\Console\Commands;

use App\Models\ApiCriteria;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class ApiSwitcherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:switcher 
                            {action : The action to perform (list|show|create|update|delete|toggle)}
                            {--id= : The ID of the API criteria}
                            {--name= : Name of the API criteria}
                            {--path= : Path of the API endpoint}
                            {--method= : HTTP method (GET, POST, PUT, DELETE, etc.)}
                            {--type= : Type of the API criteria (real, mock_200, mock_400, mock_500)}
                            {--status_code= : HTTP status code for the response}
                            {--body= : JSON-encoded body for mock responses}
                            {--is_active= : Whether the criteria is active (0 or 1)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage API Switcher criteria';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'list':
                $this->listCriteria();
                break;
                
            case 'show':
                $this->showCriteria();
                break;
                
            case 'create':
                $this->createCriteria();
                break;
                
            case 'update':
                $this->updateCriteria();
                break;
                
            case 'delete':
                $this->deleteCriteria();
                break;
                
            case 'toggle':
                $this->toggleCriteria();
                break;
                
            default:
                $this->error("Unknown action: {$action}. Use list, show, create, update, delete, or toggle.");
                return 1;
        }
        
        return 0;
    }
    
    /**
     * List all API criteria.
     */
    protected function listCriteria()
    {
        $criteria = ApiCriteria::all();
        
        if ($criteria->isEmpty()) {
            $this->info('No API criteria found.');
            return;
        }
        
        $headers = ['ID', 'Name', 'Path', 'Method', 'Type', 'Status', 'Active'];
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
    }
    
    /**
     * Show details of a specific API criteria.
     */
    protected function showCriteria()
    {
        $id = $this->option('id');
        
        if (!$id) {
            $this->error('The --id option is required for the show action.');
            return;
        }
        
        $criteria = ApiCriteria::find($id);
        
        if (!$criteria) {
            $this->error("API criteria with ID {$id} not found.");
            return;
        }
        
        $this->info("API Criteria #{$criteria->id}: {$criteria->name}");
        $this->line('-------------------------');
        $this->line("Path: {$criteria->path}");
        $this->line("Method: {$criteria->method}");
        $this->line("Type: {$criteria->type}");
        $this->line("Status Code: {$criteria->status_code}");
        $this->line("Content Type: {$criteria->content_type}");
        $this->line("Active: " . ($criteria->is_active ? 'Yes' : 'No'));
        
        if ($criteria->body) {
            $this->line("\nResponse Body:");
            $this->line(json_encode($criteria->body, JSON_PRETTY_PRINT));
        }
        
        if ($criteria->headers) {
            $this->line("\nResponse Headers:");
            $this->line(json_encode($criteria->headers, JSON_PRETTY_PRINT));
        }
        
        if ($criteria->query_params) {
            $this->line("\nQuery Parameters:");
            $this->line(json_encode($criteria->query_params, JSON_PRETTY_PRINT));
        }
        
        $this->line("\nCreated: {$criteria->created_at}");
        $this->line("Updated: {$criteria->updated_at}");
    }
    
    /**
     * Create a new API criteria.
     */
    protected function createCriteria()
    {
        $data = [
            'name' => $this->option('name'),
            'path' => $this->option('path'),
            'method' => $this->option('method'),
            'type' => $this->option('type'),
            'status_code' => $this->option('status_code'),
        ];
        
        // Validate required fields
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'path' => 'required|string',
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE,OPTIONS,HEAD',
            'type' => 'required|in:real,mock_200,mock_400,mock_500',
            'status_code' => 'required|integer',
        ]);
        
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return;
        }
        
        // Add optional fields
        if ($this->option('body')) {
            $bodyJson = $this->option('body');
            $data['body'] = json_decode($bodyJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON in the --body option: ' . json_last_error_msg());
                return;
            }
        }
        
        $data['is_active'] = (bool) $this->option('is_active') ?? true;
        $data['content_type'] = 'application/json';
        
        // Create the criteria
        $criteria = ApiCriteria::create($data);
        
        $this->info("API criteria '{$criteria->name}' created with ID {$criteria->id}");
    }
    
    /**
     * Update an existing API criteria.
     */
    protected function updateCriteria()
    {
        $id = $this->option('id');
        
        if (!$id) {
            $this->error('The --id option is required for the update action.');
            return;
        }
        
        $criteria = ApiCriteria::find($id);
        
        if (!$criteria) {
            $this->error("API criteria with ID {$id} not found.");
            return;
        }
        
        // Collect the data to update
        $data = [];
        
        if ($this->option('name')) {
            $data['name'] = $this->option('name');
        }
        
        if ($this->option('path')) {
            $data['path'] = $this->option('path');
        }
        
        if ($this->option('method')) {
            $data['method'] = $this->option('method');
        }
        
        if ($this->option('type')) {
            $data['type'] = $this->option('type');
        }
        
        if ($this->option('status_code')) {
            $data['status_code'] = $this->option('status_code');
        }
        
        if ($this->option('is_active') !== null) {
            $data['is_active'] = (bool) $this->option('is_active');
        }
        
        if ($this->option('body')) {
            $bodyJson = $this->option('body');
            $data['body'] = json_decode($bodyJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON in the --body option: ' . json_last_error_msg());
                return;
            }
        }
        
        if (empty($data)) {
            $this->warn('No fields provided to update.');
            return;
        }
        
        // Update the criteria
        $criteria->update($data);
        
        $this->info("API criteria '{$criteria->name}' updated successfully.");
    }
    
    /**
     * Delete an API criteria.
     */
    protected function deleteCriteria()
    {
        $id = $this->option('id');
        
        if (!$id) {
            $this->error('The --id option is required for the delete action.');
            return;
        }
        
        $criteria = ApiCriteria::find($id);
        
        if (!$criteria) {
            $this->error("API criteria with ID {$id} not found.");
            return;
        }
        
        $name = $criteria->name;
        $criteria->delete();
        
        $this->info("API criteria '{$name}' with ID {$id} deleted successfully.");
    }
    
    /**
     * Toggle the active status of an API criteria.
     */
    protected function toggleCriteria()
    {
        $id = $this->option('id');
        
        if (!$id) {
            $this->error('The --id option is required for the toggle action.');
            return;
        }
        
        $criteria = ApiCriteria::find($id);
        
        if (!$criteria) {
            $this->error("API criteria with ID {$id} not found.");
            return;
        }
        
        $criteria->is_active = !$criteria->is_active;
        $criteria->save();
        
        $status = $criteria->is_active ? 'active' : 'inactive';
        $this->info("API criteria '{$criteria->name}' is now {$status}.");
    }
}
