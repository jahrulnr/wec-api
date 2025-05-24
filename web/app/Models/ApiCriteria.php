<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiCriteria extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_criteria';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path',
        'method',
        'type',
        'status_code',
        'content_type',
        'headers',
        'body',
        'is_active',
        'description',
        'real_api_url'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status_code' => 'integer',
        'type' => 'string',
        'headers' => 'array',
        'body' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Find a matching API criteria for the given request path and method
     *
     * @param string $path
     * @param string $method
     * @return ApiCriteria|null
     */
    public static function findMatchingCriteria(string $path, string $method)
    {
        return self::where('path', $path)
                   ->where('method', $method)
                   ->where('is_active', true)
                   ->first();
    }
    
    /**
     * Check if this criteria is a mock response
     *
     * @return bool
     */
    public function isMock(): bool
    {
        return $this->type === 'mock';
    }
    
    /**
     * Get the real API endpoint for this criteria
     *
     * @return string
     */
    public function getRealEndpoint(): string
    {
        if (!empty($this->real_api_url)) {
            return $this->real_api_url;
        }
        // Get base URL from configuration
        $baseUrl = config('api_switcher.real_api_base_url');
        $baseUrl = rtrim($baseUrl, '/');
        $path = ltrim($this->path, '/');
        return "{$baseUrl}/{$path}";
    }
    
    /**
     * Scope a query to only include mock API criteria.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMock($query)
    {
        return $query->where('type', 'mock');
    }
    
    /**
     * Scope a query to only include real API criteria.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReal($query)
    {
        return $query->where('type', 'real');
    }
}
