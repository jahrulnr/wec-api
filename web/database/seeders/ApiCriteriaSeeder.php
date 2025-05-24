<?php

namespace Database\Seeders;

use App\Models\ApiCriteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApiCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example real API endpoint
        ApiCriteria::create([
            'name' => 'Get Users',
            'path' => 'users',
            'method' => 'GET',
            'type' => 'real',
            'status_code' => 200,
            'content_type' => 'application/json',
            'is_active' => true,
            'description' => 'Get list of users from real API'
        ]);
        
        // Example mock API with status 200
        ApiCriteria::create([
            'name' => 'Get Products',
            'path' => 'products',
            'method' => 'GET',
            'type' => 'mock',
            'status_code' => 200,
            'content_type' => 'application/json',
            'body' => [
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Product 1',
                        'price' => 100
                    ],
                    [
                        'id' => 2,
                        'name' => 'Product 2',
                        'price' => 200
                    ]
                ],
                'meta' => [
                    'total' => 2
                ]
            ],
            'is_active' => true,
            'description' => 'Mock products list with 200 response'
        ]);
        
        // Example mock API with status 400
        ApiCriteria::create([
            'name' => 'Create User Error',
            'path' => 'users/create',
            'method' => 'POST',
            'type' => 'mock',
            'status_code' => 400,
            'content_type' => 'application/json',
            'body' => [
                'message' => 'Validation failed',
                'errors' => [
                    'name' => ['The name field is required'],
                    'email' => ['The email field is required']
                ]
            ],
            'is_active' => true,
            'description' => 'Mock user creation error with 400 response'
        ]);
        
        // Example mock API with status 500
        ApiCriteria::create([
            'name' => 'Server Error',
            'path' => 'orders',
            'method' => 'POST',
            'type' => 'mock',
            'status_code' => 500,
            'content_type' => 'application/json',
            'body' => [
                'message' => 'Internal server error',
                'error' => 'Database connection failed'
            ],
            'is_active' => true,
            'description' => 'Mock server error with 500 response'
        ]);
    }
}
