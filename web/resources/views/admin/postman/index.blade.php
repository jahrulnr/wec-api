@extends('layouts.main')

@section('title', 'API Postman')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('postman.execute') }}" method="POST" id="apiPostmanForm">
      @csrf
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="url">Full API URL</label>
            <input type="text" class="form-control" id="url" name="url" placeholder="https://your-api-url.com/api/endpoint" required value="{{ old('url', $old['url'] ?? '') }}">
            <small class="form-text text-muted">Enter the full URL of the API endpoint you want to test.</small>
          </div>
          <div class="form-group mt-3">
            <label for="method">HTTP Method</label>
            <select class="form-control" id="method" name="method">
              <option value="GET" {{ old('method', $old['method'] ?? '') == 'GET' ? 'selected' : '' }}>GET</option>
              <option value="POST" {{ old('method', $old['method'] ?? '') == 'POST' ? 'selected' : '' }}>POST</option>
              <option value="PUT" {{ old('method', $old['method'] ?? '') == 'PUT' ? 'selected' : '' }}>PUT</option>
              <option value="PATCH" {{ old('method', $old['method'] ?? '') == 'PATCH' ? 'selected' : '' }}>PATCH</option>
              <option value="DELETE" {{ old('method', $old['method'] ?? '') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
              <option value="OPTIONS" {{ old('method', $old['method'] ?? '') == 'OPTIONS' ? 'selected' : '' }}>OPTIONS</option>
            </select>
          </div>
          <div class="form-group mt-3">
            <label for="headers">Request Headers (JSON)</label>
            <textarea class="form-control" id="headers" name="headers" rows="3" style="font-family: monospace;">{{ old('headers', $old['headers'] ?? "{\n  \"Content-Type\": \"application/json\",\n  \"Accept\": \"application/json\"\n}") }}</textarea>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="body">Request Body (JSON - for POST, PUT, PATCH)</label>
            <textarea class="form-control" id="body" name="body" rows="9" style="font-family: monospace;">{{ old('body', $old['body'] ?? '{}') }}</textarea>
          </div>
        </div>
      </div>
      <div class="form-group mt-4">
        <button type="submit" class="btn btn-primary">Send Request</button>
      </div>
    </form>
    @if ($error ?? false)
      <div class="alert alert-danger mt-3">{{ $error }}</div>
    @endif
    @if ($response ?? false)
    <hr>
    <h5>Response Details</h5>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Status Code</label>
          <div class="alert {{ $response['status_code'] < 400 ? 'alert-success' : 'alert-danger' }}">
            {{ $response['status_code'] }} {{ $response['status_text'] ?? '' }}
          </div>
        </div>
        <div class="form-group">
          <label>Response Time</label>
          <div>{{ $response['time'] ?? '-' }} ms</div>
        </div>
        <div class="form-group">
          <label>Content Type</label>
          <div>{{ $response['content_type'] ?? '-' }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Response Headers</label>
          <pre class="bg-light p-2" style="max-height: 150px; overflow-y: auto;">{{ json_encode($response['headers'] ?? [], JSON_PRETTY_PRINT) }}</pre>
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label>Response Body</label>
          <pre class="bg-light p-2" style="max-height: 400px; overflow-y: auto; white-space: pre-wrap;">{{ $response['body'] ?? '' }}</pre>
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
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // CodeMirror for headers
    var headersEditor = CodeMirror.fromTextArea(document.getElementById('headers'), {
      mode: {name: "javascript", json: true},
      theme: "material-darker",
      lineNumbers: true,
      lineWrapping: true,
      matchBrackets: true,
      autoCloseBrackets: true,
      tabSize: 2,
      viewportMargin: Infinity,
      height: 'auto',
    });
    // CodeMirror for body
    var bodyEditor = CodeMirror.fromTextArea(document.getElementById('body'), {
      mode: {name: "javascript", json: true},
      theme: "material-darker",
      lineNumbers: true,
      lineWrapping: true,
      matchBrackets: true,
      autoCloseBrackets: true,
      tabSize: 2,
      viewportMargin: Infinity,
      height: 'auto',
    });
    // Sync CodeMirror content to textarea before submit
    document.getElementById('apiPostmanForm').addEventListener('submit', function(e) {
      headersEditor.save();
      bodyEditor.save();
      const headers = headersEditor.getValue().trim();
      const body = bodyEditor.getValue().trim();
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
    });
    document.getElementById('method').addEventListener('change', function(e) {
      const method = e.target.value;
      if (["POST", "PUT", "PATCH"].includes(method)) {
        bodyEditor.setOption('readOnly', false);
        bodyEditor.getWrapperElement().style.opacity = '1';
      } else {
        bodyEditor.setOption('readOnly', true);
        bodyEditor.getWrapperElement().style.opacity = '0.5';
      }
    });
    // Set initial state for body
    if (["POST", "PUT", "PATCH"].includes(document.getElementById('method').value)) {
      bodyEditor.setOption('readOnly', false);
      bodyEditor.getWrapperElement().style.opacity = '1';
    } else {
      bodyEditor.setOption('readOnly', true);
      bodyEditor.getWrapperElement().style.opacity = '0.5';
    }
  });
</script>
@endpush
