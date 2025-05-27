<?php

namespace App\Http\Middleware;

use App\Models\ApiCriteria;
use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ApiSwitcherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        // Check if API switcher is enabled
        if (!config('api_switcher.enabled', true)) {
            return $next($request);
        }
        
        $path = '/'.$request->path();
        // Remove 'api/' prefix if it exists
        $path = preg_replace('#^'.parse_url(config('app.url'))['path'].'/api/#', '', $path);
        $method = $request->method();
        
        // Log the incoming request if logging is enabled
        if (config('api_switcher.logging.enabled', true)) {
            $logContext = [
                'ip' => $request->ip()
            ];
            
            // Include request body if configured
            if (config('api_switcher.logging.include_request_body', true)) {
                $logContext['body'] = $request->all();
            }
            
            // Log to both file and database
            Log::info("API Switcher: Incoming request", $logContext);
            ApiLog::logRequest($path, $method, $logContext);
        }
        
        // Attempt to find matching criteria
        $criteria = ApiCriteria::findMatchingCriteria($path, $method);
        
        // If no criteria found, handle according to default behavior
        if (!$criteria) {
            Log::info("API Switcher: No matching criteria for path {$path} and method {$method}");
            $defaultBehavior = config('api_switcher.default_behavior', 'pass');
            
            if ($defaultBehavior === 'pass') {
                return $next($request);
            } elseif ($defaultBehavior === 'mock') {
                // Create a default criteria for the mock response
                $defaultCriteria = new ApiCriteria([
                    'path' => $path,
                    'method' => $method,
                    'type' => 'mock',
                    'status_code' => 200,
                    'content_type' => 'application/json',
                    'body' => ['message' => "Default mock response"]
                ]);
                
                return $this->createMockResponse($defaultCriteria);
            } else { // 'real'
                // Create a default criteria for the real API
                $defaultCriteria = new ApiCriteria([
                    'path' => $path,
                    'method' => $method,
                    'type' => 'real',
                ]);
                
                return $this->forwardToRealApi($request, $defaultCriteria);
            }
        }
        
        // If it's a mock response, return it immediately
        if ($criteria->isMock()) {
            return $this->createMockResponse($criteria);
        }
        
        // If it's a real API request, forward it to the actual API
        return $this->forwardToRealApi($request, $criteria);
    }
    
    /**
     * Create a mock response based on the criteria
     *
     * @param ApiCriteria $criteria
     * @return Response
     */
    private function createMockResponse(ApiCriteria $criteria): Response
    {
        $headers = $criteria->headers ?? [];
        $body = $criteria->body ?? [];
        
        $response = response()
            ->json($body, $criteria->status_code);
        
        foreach ($headers as $name => $value) {
            $response->header($name, $value);
        }            // Log the response if logging is enabled
            if (config('api_switcher.logging.enabled')) {
                $logData = [
                    'status' => $criteria->status_code,
                    'type' => $criteria->type
                ];
                
                if (config('api_switcher.logging.include_response_body')) {
                    $logData['body'] = $body;
                }
                
                // Log to both file and database
                Log::info("API Switcher: Returning mock response", $logData);
                ApiLog::logResponse(
                    $criteria->path, 
                    $criteria->method, 
                    'mock', 
                    $criteria->status_code, 
                    ['body' => $body]
                );
            }
        
        return $response;
    }
    
    /**
     * Forward the request to the real API using Guzzle directly
     *
     * @param Request $request
     * @param ApiCriteria $criteria
     * @return Response
     */
    private function forwardToRealApi(Request $request, ApiCriteria $criteria): Response
    {
        try {
            $endpoint = $criteria->getRealEndpoint();
            $method = strtoupper($request->method());

            // Check if we should use caching
            $shouldCache = config('api_switcher.cache.enabled') && strtolower($method) === 'get';
            if ($shouldCache) {
                $cacheKey = $this->generateCacheKey($endpoint, $request->all());
                $ttl = config('api_switcher.cache.ttl', 3600);
                $cachedResponse = Cache::get($cacheKey);
                if ($cachedResponse) {
                    Log::info("API Switcher: Returning cached response for {$endpoint}");
                    return response($cachedResponse['body'], $cachedResponse['status'])
                        ->withHeaders($cachedResponse['headers']);
                }
            }

            // Forward all headers except Host
            $headers = $request->headers->all();
            unset($headers['host']);
            
            // Flatten headers for Guzzle
            $flatHeaders = [];
            foreach ($headers as $key => $value) {
                $flatHeaders[$key] = is_array($value) ? implode(", ", $value) : $value;
            }

            // Create a new Guzzle client
            $client = new Client([
                'timeout' => config('api_switcher.http_client.timeout', 30),
                'connect_timeout' => config('api_switcher.http_client.connect_timeout', 10),
                'http_errors' => false,
                'verify' => false,
            ]);
            
            // Prepare request options
            $options = [
                'headers' => $flatHeaders,
                'query' => $request->query(),
                'http_errors' => false,
                'verify' => false,
            ];
            
            // Add cookies if present
            if (count($request->cookies) > 0) {
                $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray(
                    $request->cookies->all(),
                    parse_url($endpoint, PHP_URL_HOST)
                );
                $options['cookies'] = $cookieJar;
            }
            
            // Handle file uploads and body based on request method
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                if ($request->hasFile(null)) {
                    // Handle multipart/form-data with file uploads
                    $options['multipart'] = [];
                    
                    foreach ($request->allFiles() as $name => $file) {
                        $options['multipart'][] = [
                            'name' => $name,
                            'contents' => fopen($file->getRealPath(), 'r'),
                            'filename' => $file->getClientOriginalName(),
                        ];
                    }
                    
                    // Add other form fields
                    foreach ($request->except(array_keys($request->allFiles())) as $key => $value) {
                        $options['multipart'][] = [
                            'name' => $key,
                            'contents' => $value,
                        ];
                    }
                } else {
                    // Handle raw body content (JSON, form, etc.)
                    $options['body'] = $request->getContent();
                }
            }
            
            // Execute the request
            $response = $client->request($method, $endpoint, $options);
            
            // Process response
            $responseBody = (string) $response->getBody();
            $statusCode = $response->getStatusCode();
            $responseHeaders = $response->getHeaders();
            
            // Log the response if logging is enabled
            if (config('api_switcher.logging.enabled')) {
                $logData = [
                    'method' => $method,
                    'url' => $endpoint,
                    'status' => $statusCode,
                ];
                
                if (config('api_switcher.logging.include_response_body')) {
                    // Try to parse as JSON if possible
                    try {
                        $responseBodyForLog = json_decode($responseBody, true);
                        $logData['response'] = $responseBodyForLog;
                    } catch (\Exception $e) {
                        $logData['response'] = '[Non-JSON response]';
                        $responseBodyForLog = '[Non-JSON response]';
                    }
                }
                
                Log::info("API Switcher: Forwarded request to real API", $logData);
                
                // Log to database
                ApiLog::logResponse(
                    $criteria->path,
                    $criteria->method,
                    'real',
                    $statusCode,
                    config('api_switcher.logging.include_response_body') ? 
                        ['body' => $responseBodyForLog ?? '[Non-JSON response]'] : []
                );
            }

            // Cache the response if caching is enabled and this is a GET request
            if ($shouldCache) {
                $cachedData = [
                    'body' => $responseBody,
                    'status' => $statusCode,
                    'headers' => $responseHeaders
                ];
                Cache::put($cacheKey, $cachedData, $ttl);
            }

            // Return Laravel response from Guzzle response
            return response($responseBody, $statusCode)
                ->withHeaders($responseHeaders);
                
        } catch (\Exception $e) {
            Log::error("API Switcher: Error forwarding to real API: " . $e->getMessage(), [
                'exception' => $e,
                'endpoint' => $endpoint ?? null,
                'method' => $method ?? null
            ]);
            
            // Log error to database
            ApiLog::logError(
                $criteria->path,
                $criteria->method,
                $e->getMessage(),
                [
                    'endpoint' => $endpoint ?? null,
                    'trace' => $e->getTraceAsString()
                ]
            );
            
            return response()->json([
                'error' => 'Failed to forward request to real API',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate a cache key for the API request
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    private function generateCacheKey(string $url, array $params): string
    {
        // Sort the parameters to ensure consistent cache keys
        ksort($params);
        $paramString = json_encode($params);
        
        return 'api_switcher:' . md5($url . $paramString);
    }
}
