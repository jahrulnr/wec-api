<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostmanController extends Controller
{
    
    /**
     * Show the Postman-like API tester page
     */
    public function postman(Request $request)
    {
        // Pass old input if available for form repopulation
        return view('admin.postman.index', [
            'old' => old() ?: [],
            'response' => session('response'),
            'error' => session('error'),
        ]);
    }

    /**
     * Execute a custom API request from the Postman page
     */
    public function executePostman(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE,OPTIONS',
            'headers' => 'nullable|string',
            'body' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $url = $request->url;
        $method = strtoupper($request->method);
        $headers = [];
        $body = $request->body;
        if ($request->headers) {
            $headers = json_decode($request->headers, true);
        }
        $options = [
            'headers' => $headers,
            'http_errors' => false,
            'verify' => false,
        ];
        if (in_array($method, ['POST', 'PUT', 'PATCH']) && $body) {
            $options['body'] = $body;
        }
        $client = new \GuzzleHttp\Client();
        try {
            $start = microtime(true);
            $response = $client->request($method, $url, $options);
            $responseTime = round((microtime(true) - $start) * 1000);
            $responseHeaders = $response->getHeaders();
            $headersArr = [];
            foreach ($responseHeaders as $k => $v) {
                $headersArr[$k] = implode('; ', $v);
            }
            $contentType = $response->getHeaderLine('Content-Type');
            $bodyContent = (string) $response->getBody();
            // Store response in session and redirect to GET for PRG pattern
            return redirect()->route('postman')->withInput()->with('response', [
                'status_code' => $response->getStatusCode(),
                'status_text' => $response->getReasonPhrase(),
                'headers' => $headersArr,
                'body' => $bodyContent,
                'content_type' => $contentType,
                'time' => $responseTime,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('postman')->withInput()->with('error', $e->getMessage());
        }
    }
}
