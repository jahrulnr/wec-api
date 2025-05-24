<?php

namespace App\Console\Commands;

use App\Models\ApiCriteria;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class ApiCheckEndpoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:check-endpoint {path} {method=GET} {--force-real : Force using real API} {--force-mock : Force using mock}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check how an API endpoint will be handled by the API Switcher';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');
        $method = strtoupper($this->argument('method'));
        $forceReal = $this->option('force-real');
        $forceMock = $this->option('force-mock');

        if ($forceReal && $forceMock) {
            $this->error('Cannot force both real and mock. Choose one.');
            return 1;
        }

        $criteria = \App\Models\ApiCriteria::where('path', $path)
            ->where('method', $method)
            ->first();

        if (!$criteria) {
            $this->warn("No API criteria found for path '{$path}' and method '{$method}'.");
            $default = config('api_switcher.default_behavior', 'pass');
            $this->info("Default behavior: {$default}");
            return 0;
        }

        $this->info("API Criteria found:");
        $this->line("ID: {$criteria->id}");
        $this->line("Name: {$criteria->name}");
        $this->line("Type: {$criteria->type}");
        $this->line("Active: " . ($criteria->is_active ? 'Yes' : 'No'));
        $this->line("Status Code: {$criteria->status_code}");
        $this->line("Description: {$criteria->description}");

        if ($forceReal) {
            $this->info('Request will be FORWARDED to REAL API (forced by option)');
        } elseif ($forceMock) {
            $this->info('Request will be MOCKED (forced by option)');
        } else {
            if ($criteria->type === 'real') {
                $this->info('Request will be FORWARDED to REAL API');
            } else {
                $this->info('Request will be MOCKED');
            }
        }
        return 0;
    }
}
