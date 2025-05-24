<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiSwitcherController extends Controller
{
    /**
     * Display a listing of the API criteria.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $criteria = ApiCriteria::all();
        return response()->json($criteria);
    }

    /**
     * Store a newly created API criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
            'headers' => 'nullable|array',
            'body' => 'nullable',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Handle JSON body if it's a string
        if (is_string($request->body) && is_array(json_decode($request->body, true))) {
            $request->merge(['body' => json_decode($request->body, true)]);
        }
        
        $criteria = ApiCriteria::create($request->all());
        return response()->json($criteria, 201);
    }

    /**
     * Display the specified API criteria.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $criteria = ApiCriteria::find($id);
        
        if (!$criteria) {
            return response()->json(['error' => 'API Criteria not found'], 404);
        }
        
        return response()->json($criteria);
    }

    /**
     * Update the specified API criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $criteria = ApiCriteria::find($id);
        
        if (!$criteria) {
            return response()->json(['error' => 'API Criteria not found'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'path' => 'string',
            'method' => 'in:GET,POST,PUT,PATCH,DELETE,OPTIONS,HEAD',
            'type' => 'in:real,mock',
            'status_code' => 'integer',
            'content_type' => 'nullable|string',
            'headers' => 'nullable|array',
            'body' => 'nullable',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Handle JSON body if it's a string
        if (is_string($request->body) && is_array(json_decode($request->body, true))) {
            $request->merge(['body' => json_decode($request->body, true)]);
        }
        
        $criteria->update($request->all());
        return response()->json($criteria);
    }

    /**
     * Remove the specified API criteria.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $criteria = ApiCriteria::find($id);
        
        if (!$criteria) {
            return response()->json(['error' => 'API Criteria not found'], 404);
        }
        
        $criteria->delete();
        return response()->json(['message' => 'API Criteria deleted successfully']);
    }
    
    /**
     * Toggle the active status of an API criteria.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActive($id)
    {
        $criteria = ApiCriteria::find($id);
        
        if (!$criteria) {
            return response()->json(['error' => 'API Criteria not found'], 404);
        }
        
        $criteria->is_active = !$criteria->is_active;
        $criteria->save();
        
        return response()->json([
            'message' => 'API Criteria status toggled successfully',
            'is_active' => $criteria->is_active
        ]);
    }
}
