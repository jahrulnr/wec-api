@extends('layouts.main')

@section('title', 'Test API Endpoint')

@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Test API Endpoint</h3>
  </div>
  <div class="card-body">
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <form action="{{ route('api-switcher.test.execute') }}" method="POST" id="apiTestForm">
      @csrf
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="method">HTTP Method</label>
            <select class="form-control" id="method" name="method">
              <option value="GET">GET</option>
              <option value="POST">POST</option>
              <option value="PUT">PUT</option>
              <option value="PATCH">PATCH</option>
              <option value="DELETE">DELETE</option>
              <option value="OPTIONS">OPTIONS</option>
            </select>
          </div>
          
          <div class="form-group mt-3">
            <label for="path">API Path</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">{{ parse_url(config('app.url'))['path'] }}/api/</span>
              </div>
              <select class="form-control" id="path" name="path" required>
                <option value="" disabled selected>Select API Path</option>
                @foreach(\App\Models\ApiCriteria::where('is_active', true)->orderBy('path')->get() as $criteria)
                  <option value="{{ $criteria->path }}">{{ $criteria->path }} ({{ $criteria->method }})</option>
                @endforeach
              </select>
            </div>
            <small class="form-text text-muted">Select from available API criteria</small>
          </div>
          
          <div class="form-group mt-3">
            <label for="headers">Request Headers (JSON)</label>
            <textarea class="form-control" id="headers" name="headers" rows="3" style="font-family: monospace;">{
  "Content-Type": "application/json",
  "Accept": "application/json"
}</textarea>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            <label for="body">Request Body (JSON - for POST, PUT, PATCH)</label>
            <textarea class="form-control" id="body" name="body" rows="9" style="font-family: monospace;">{}</textarea>
          </div>
          
          <div class="form-group mt-3">
            <label>Test Options</label>
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="force_real" name="force_real" value="1">
              <label class="form-check-label" for="force_real">Force real API (bypass API Switcher)</label>
            </div>
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="force_mock" name="force_mock" value="1">
              <label class="form-check-label" for="force_mock">Force mock response (if available)</label>
            </div>
          </div>
        </div>
      </div>
      
      <div class="form-group mt-4">
        <button type="submit" class="btn btn-primary">Send Request</button>
      </div>
    </form>
    
    @if (isset($response))
    <hr>
    <h5>Response Details</h5>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Status Code</label>
          <div class="alert {{ $response['status_code'] < 400 ? 'alert-success' : 'alert-danger' }}">
            {{ $response['status_code'] }} {{ $response['status_text'] }}
          </div>
        </div>
        
        <div class="form-group">
          <label>Response Time</label>
          <div>{{ $response['time'] }} ms</div>
        </div>
        
        <div class="form-group">
          <label>Content Type</label>
          <div>{{ $response['content_type'] }}</div>
        </div>
        
        <div class="form-group">
          <label>API Mode</label>
          <div class="badge {{ $response['mode'] === 'real' ? 'badge-primary' : 'badge-warning' }}">
            {{ strtoupper($response['mode']) }}
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="form-group">
          <label>Response Headers</label>
          <pre class="bg-light p-2" style="max-height: 150px; overflow-y: auto;">{{ json_encode($response['headers'], JSON_PRETTY_PRINT) }}</pre>
        </div>
      </div>
      
      <div class="col-12">
        <div class="form-group">
          <label>Response Body</label>
          <pre class="bg-light p-2" style="max-height: 400px; overflow-y: auto; white-space: pre-wrap;">{{ $response['body'] }}</pre>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/material-darker.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Toggle body field based on method
    document.getElementById('method').addEventListener('change', function(e) {
      const method = e.target.value;
      const bodyField = document.getElementById('body');
      
      if (['POST', 'PUT', 'PATCH'].includes(method)) {
        bodyField.disabled = false;
        bodyField.parentElement.style.opacity = '1';
      } else {
        bodyField.disabled = true;
        bodyField.parentElement.style.opacity = '0.5';
      }
    });
    
    // Validate JSON fields
    document.getElementById('apiTestForm').addEventListener('submit', function(e) {
      const headers = document.getElementById('headers').value.trim();
      const body = document.getElementById('body').value.trim();
      let hasError = false;
      
      if (headers) {
        try {
          JSON.parse(headers);
        } catch (error) {
          e.preventDefault();
          alert('Invalid JSON in Headers: ' + error.message);
          hasError = true;
        }
      }
      
      if (!hasError && body && !document.getElementById('body').disabled) {
        try {
          JSON.parse(body);
        } catch (error) {
          e.preventDefault();
          alert('Invalid JSON in Request Body: ' + error.message);
        }
      }
      
      // Prevent using both force_real and force_mock
      const forceReal = document.getElementById('force_real');
      const forceMock = document.getElementById('force_mock');
      
      if (forceReal.checked && forceMock.checked) {
        e.preventDefault();
        alert('You cannot force both real API and mock response. Please select only one option.');
      }
    });

    document.querySelectorAll('pre,textarea').forEach(function(pre) {
      var code = pre.textContent;
      var mode = 'javascript';
      var isLikelyJson = code.trim().startsWith('{') || code.trim().startsWith('[');
      if (isLikelyJson) {
        try {
          var json = JSON.parse(code);
          code = JSON.stringify(json, null, 2);
          mode = {name: 'javascript', json: true};
        } catch (e) {
          mode = 'htmlmixed';
        }
      } else {
        mode = 'htmlmixed';
      }
      var cm = CodeMirror(function(elt) {
        pre.parentNode.replaceChild(elt, pre);
      }, {
        value: code,
        mode: mode,
        theme: "material-darker",
        lineNumbers: true,
        lineWrapping: true,
        readOnly: true,
        tabSize: 2,
        viewportMargin: Infinity,
        height: 'auto',
      });
    });
  });
</script>
@endpush