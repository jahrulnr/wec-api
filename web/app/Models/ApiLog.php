<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ApiLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'path',
        'method',
        'ip',
        'type',
        'status_code',
        'response_type',
        'request_body',
        'response_body',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'request_body' => 'json',
        'response_body' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Log a request to the database and prune old logs
     *
     * @param string $path
     * @param string $method
     * @param array $data
     * @return void
     */
    public static function logRequest(string $path, string $method, array $data = []): void
    {
        self::create([
            'path' => $path,
            'method' => $method,
            'ip' => $data['ip'] ?? null,
            'type' => 'request',
            'request_body' => $data['body'] ?? null,
        ]);
        
        // Prune logs if more than 1000 records exist
        self::pruneOldLogs();
    }
    
    /**
     * Log a response to the database
     *
     * @param string $path
     * @param string $method
     * @param string $responseType
     * @param int $statusCode
     * @param array $data
     * @return void
     */
    public static function logResponse(string $path, string $method, string $responseType, int $statusCode, array $data = []): void
    {
        self::create([
            'path' => $path,
            'method' => $method,
            'type' => 'response',
            'status_code' => $statusCode,
            'response_type' => $responseType,
            'response_body' => $data['body'] ?? null,
        ]);
        
        // Prune logs if more than 1000 records exist
        self::pruneOldLogs();
    }
    
    /**
     * Log an error to the database
     *
     * @param string $path
     * @param string $method
     * @param string $error
     * @param array $data
     * @return void
     */
    public static function logError(string $path, string $method, string $error, array $data = []): void
    {
        self::create([
            'path' => $path,
            'method' => $method,
            'type' => 'error',
            'response_body' => ['error' => $error, 'details' => $data],
        ]);
        
        // Prune logs if more than 1000 records exist
        self::pruneOldLogs();
    }
    
    /**
     * Delete older logs if log count exceeds 1000 records
     *
     * @return void
     */
    public static function pruneOldLogs(): void
    {
        $count = self::count();
        
        if ($count > 1000) {
            // Delete the oldest logs, keeping only the latest 1000
            $oldest = self::orderBy('created_at', 'asc')
                ->limit($count - 1000)
                ->pluck('id');
                
            if (!empty($oldest)) {
                self::whereIn('id', $oldest)->delete();
            }
        }
    }
    
    /**
     * Clear all logs from the table
     *
     * @return int
     */
    public static function clearAllLogs(): int
    {
        return self::query()->delete();
    }
}
