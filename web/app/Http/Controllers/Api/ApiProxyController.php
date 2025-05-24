<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ApiSwitcherMiddleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class ApiProxyController extends Controller implements HasMiddleware
{

    /**
     * Get the middleware that should be assigned to the controller.
     */

    public static function middleware(): array
    {
        return [
            new Middleware(ApiSwitcherMiddleware::class),
        ];
    }

    /**
     * Handle any proxied API request.
     * 
     * This is a catch-all method that can handle any HTTP method
     * and will forward the request to the appropriate endpoint.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // The ApiSwitcherMiddleware should handle the routing logic
        // This controller simply logs the request if needed
        
        if (config('api_switcher.logging.enabled')) {
            Log::info('API Proxy Request', [
                'path' => $request->path(),
                'method' => $request->method(),
                'query' => $request->query(),
                'headers' => $request->headers->all(),
                'body' => config('api_switcher.logging.include_request_body') ? $request->all() : '[REDACTED]'
            ]);
        }
        
        // The middleware will have already processed the request
        // This should never be reached in normal flow
        return response()->json(['error' => 'Request not processed by API Switcher'], 500);
    }
}
