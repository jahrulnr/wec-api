<?php

namespace App\Http\Controllers;

use App\Models\ApiCriteria;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiSwitcherDashboardController extends Controller
{
    /**
     * Display the API Switcher dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $criteria = ApiCriteria::all();
        return view('admin.api-switcher.dashboard', [
            'menu' => 'API Tools',
            'menu_name' => 'API Switcher',
            'criteria' => $criteria,
        ]);
    }
    
    /**
     * Show the form for creating a new criteria
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.api-switcher.create', [
            'menu' => 'API Tools',
            'menu_name' => 'Create API Criteria',
        ]);
    }
    
    /**
     * Store a newly created criteria
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'path' => 'required|string',
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE,OPTIONS,HEAD',
            'type' => 'required|in:real,mock',
            'status_code' => 'required|integer',
            'content_type' => 'nullable|string',
            'body' => 'nullable',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'real_api_url' => 'nullable|url|max:255', // Add validation for real_api_url
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Handle JSON body if it's a string
        if ($request->has('body') && is_string($request->body)) {
            try {
                $bodyData = json_decode($request->body, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $request->merge(['body' => $bodyData]);
                }
            } catch (\Exception $e) {
                // If JSON parsing fails, use the string as is
            }
        }
        
        ApiCriteria::create($request->all());
        
        return redirect()->route('api-switcher.dashboard')
            ->with('success', 'API criteria created successfully!');
    }
    
    /**
     * Show the form for editing a criteria
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $criteria = ApiCriteria::findOrFail($id);
        
        return view('admin.api-switcher.edit', [
            'menu' => 'API Tools',
            'menu_name' => 'Edit API Criteria',
            'criteria' => $criteria,
        ]);
    }
    
    /**
     * Update the specified criteria
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $criteria = ApiCriteria::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'path' => 'string',
            'method' => 'in:GET,POST,PUT,PATCH,DELETE,OPTIONS,HEAD',
            'type' => 'in:real,mock',
            'status_code' => 'integer',
            'content_type' => 'nullable|string',
            'body' => 'nullable',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'real_api_url' => 'nullable|url|max:255', // Add validation for real_api_url
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Handle JSON body if it's a string
        if ($request->has('body') && is_string($request->body)) {
            try {
                $bodyData = json_decode($request->body, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $request->merge(['body' => $bodyData]);
                }
            } catch (\Exception $e) {
                // If JSON parsing fails, use the string as is
            }
        }
        
        $criteria->update($request->all());
        
        return redirect()->route('api-switcher.dashboard')
            ->with('success', 'API criteria updated successfully!');
    }
    
    /**
     * Delete the specified criteria
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $criteria = ApiCriteria::findOrFail($id);
        $criteria->delete();
        
        return redirect()->route('api-switcher.dashboard')
            ->with('success', 'API criteria deleted successfully!');
    }
    
    /**
     * Toggle active status of the specified criteria
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle($id)
    {
        $criteria = ApiCriteria::findOrFail($id);
        $criteria->is_active = !$criteria->is_active;
        $criteria->save();
        
        $status = $criteria->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('api-switcher.dashboard')
            ->with('success', "API criteria {$status} successfully!");
    }
    
    /**
     * Test an API endpoint
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function test(Request $request)
    {
        return view('admin.api-switcher.test', [
            'menu' => 'API Tools',
            'menu_name' => 'Test API Endpoint',
        ]);
    }
    
    /**
     * Execute API test
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function executeTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE',
            'body' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $path = $request->path;
        $method = strtolower($request->method);
        $body = $request->body;
        
        // Create the full URL
        $baseUrl = config('app.url');
        $fullUrl = rtrim($baseUrl, '/') . '/api/' . ltrim($path, '/');

        // Prepare the HTTP client
        $client = new \GuzzleHttp\Client([
            'http_errors' => false,
            'verify' => false,
        ]);
        
        $options = ['headers' => ['Accept' => 'application/json']];
        
        if ($method !== 'get' && !empty($body)) {
            // Try to parse as JSON if it's not a GET request
            try {
                $jsonBody = json_decode($body, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $options['json'] = $jsonBody;
                } else {
                    $options['body'] = $body;
                }
            } catch (\Exception $e) {
                $options['body'] = $body;
            }
        }
        
        // Execute the request
        try {
            $start = time();
            $response = $client->request($method, $fullUrl, $options);
            $responseTime = time() - $start;

            // Get the response details
            $statusCode = $response->getStatusCode();
            $headers = $response->getHeaders();
            $responseBody = $response->getBody()->getContents();
            
            // Try to format JSON for display
            $formattedBody = $responseBody;
            try {
                $jsonBody = json_decode($responseBody);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $formattedBody = json_encode($jsonBody, JSON_PRETTY_PRINT);
                }
            } catch (\Exception $e) {
                // Use the raw body if JSON parsing fails
            }

            // Compose a $result array for the view
            $result = [
                'status_code' => $statusCode,
                'status_text' => $response->getReasonPhrase(),
                'headers' => $headers,
                'body' => $formattedBody,
                'raw_body' => $responseBody,
                'time' => $responseTime,
                'mode' => ApiCriteria::findMatchingCriteria($path, strtoupper($method)) ? 
                    (ApiCriteria::findMatchingCriteria($path, strtoupper($method))->isMock() ? 'mock' : 'real') : 
                    'real',
                'request' => [
                    'method' => strtoupper($method),
                    'path' => $fullUrl,
                    'headers' => $options['headers'] ?? [],
                    'body' => $body,
                ],
            ];

            // Prepare the result view
            return view('admin.api-switcher.test-result', [
                'menu' => 'API Tools',
                'menu_name' => 'API Test Results',
                'result' => $result,
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', "Error testing API: {$e->getMessage()}")
                ->withInput();
        }
    }
    
    /**
     * Show the logs for API Switcher
     * 
     * @return \Illuminate\View\View
     */
    public function logs(Request $request)
    {
        // Apply filters if provided
        $query = ApiLog::query();
        
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'real':
                    $query->where('response_type', 'real');
                    break;
                case 'mock':
                    $query->where('response_type', 'mock');
                    break;
                case 'error':
                    $query->where(function($q) {
                        $q->where('type', 'error')
                          ->orWhere('status_code', '>=', 400);
                    });
                    break;
                default:
                    // No filter or 'all' - no additional conditions needed
                    break;
            }
        }
        
        // Get API logs from the database
        $logs = $query->orderBy('created_at', 'desc')
                      ->limit(100)
                      ->get();
        
        // Get log statistics
        $stats = [
            'total' => ApiLog::count(),
            'requests' => ApiLog::where('type', 'request')->count(),
            'responses' => ApiLog::where('type', 'response')->count(),
            'errors' => ApiLog::where('type', 'error')->count(),
            'mock' => ApiLog::where('response_type', 'mock')->count(),
            'real' => ApiLog::where('response_type', 'real')->count(),
        ];
        
        return view('admin.api-switcher.logs', [
            'menu' => 'API Tools',
            'menu_name' => 'API Switcher Logs',
            'logs' => $logs,
            'stats' => $stats,
        ]);
    }
    
    /**
     * Clear API logs
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearLogs()
    {
        $count = ApiLog::clearAllLogs();
        
        return redirect()->route('api-switcher.logs')
            ->with('success', "Successfully cleared {$count} log entries.");
    }
}
