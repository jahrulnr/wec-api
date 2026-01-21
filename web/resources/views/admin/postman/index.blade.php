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
            <input type="text" class="form-control" id="url" name="url" placeholder="https://your-api-url.com/api/endpoint or paste curl command" required value="{{ old('url', $old['url'] ?? '') }}">
            <small class="form-text text-muted">Enter the full URL of the API endpoint you want to test or paste a curl command.</small>
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
            <textarea class="form-control" id="headers" name="reqHeaders" rows="3" style="font-family: monospace;">{{ old('reqHeaders', $old['reqHeaders'] ?? "{\n  \"Content-Type\": \"application/json\",\n  \"Accept\": \"application/json\"\n}") }}</textarea>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="body">Request Body (JSON - for POST, PUT, PATCH)</label>
            <textarea class="form-control" id="body" name="reqBody" rows="9" style="font-family: monospace;">{{ old('reqBody', $old['reqBody'] ?? '{}') }}</textarea>
          </div>
        </div>
      </div>
      <div class="form-group mt-4 d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">Send Request</button>
        <button type="button" id="generateCurl" class="btn btn-secondary">Generate cURL Command</button>
      </div>
    </form>

    <!-- cURL Command Display Section -->
    <div id="curlCommandContainer" class="mt-4 d-none">
      <div class="card bg-dark">
        <div class="card-header text-white bg-dark">
          <div class="d-flex justify-content-between">
            <h5 class="mb-0">cURL Command</h5>
            <button type="button" id="copyCurlCommand" class="btn btn-sm btn-outline-light">Copy</button>
          </div>
        </div>
        <div class="card-body">
          <pre id="curlCommand" class="mb-0 text-light" style="white-space: pre-wrap; word-break: break-all;"></pre>
        </div>
      </div>
    </div>
    
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

    // cURL Command Generation
    document.getElementById('generateCurl').addEventListener('click', function() {
      headersEditor.save();
      bodyEditor.save();
      const url = document.getElementById('url').value.trim();
      const method = document.getElementById('method').value;
      const headers = headersEditor.getValue().trim();
      const body = bodyEditor.getValue().trim();

      let curlCommand = `curl -X ${method} "${url}"`;

      // Add headers to cURL command
      if (headers) {
        try {
          const jsonHeaders = JSON.parse(headers);
          Object.keys(jsonHeaders).forEach(key => {
            const value = jsonHeaders[key];
            curlCommand += ` -H "${key}: ${value}"`;
          });
        } catch (error) {
          alert('Invalid JSON in Headers: ' + error.message);
          return;
        }
      }

      // Add body to cURL command if method is POST, PUT, or PATCH
      if (["POST", "PUT", "PATCH"].includes(method) && body) {
        try {
          // Format JSON body with indentation
          const jsonBody = JSON.stringify(JSON.parse(body), null, 2);
          curlCommand += ` -d '${jsonBody}'`;
        } catch (error) {
          alert('Invalid JSON in Request Body: ' + error.message);
          return;
        }
      }

      // Display the generated cURL command
      document.getElementById('curlCommand').textContent = curlCommand;
      document.getElementById('curlCommandContainer').classList.remove('d-none');
    });

    // Copy cURL command to clipboard
    document.getElementById('copyCurlCommand').addEventListener('click', function() {
      const curlCommand = document.getElementById('curlCommand');
      curlCommand.classList.remove('text-light');
      curlCommand.classList.add('bg-white', 'border', 'rounded');
      const range = document.createRange();
      range.selectNode(curlCommand);
      window.getSelection().removeAllRanges();
      window.getSelection().addRange(range);
      document.execCommand('copy');
      window.getSelection().removeAllRanges();
      curlCommand.classList.add('text-light');
      curlCommand.classList.remove('bg-white', 'border', 'rounded');
      alert('cURL command copied to clipboard!');
    });

    // cURL Command Parser
    document.getElementById('url').addEventListener('blur', function() {
      const curlCommand = this.value.trim();
      if (curlCommand.startsWith('curl ')) {
        try {
          // Parse the curl command
          let parsedCommand = {
            method: 'GET', // Default method
            url: '',
            headers: {},
            body: ''
          };

          // Extract method (-X or --request)
          const methodMatch = curlCommand.match(/(?:\s-X|\s--request)\s+([A-Z]+)/i);
          if (methodMatch && methodMatch[1]) {
            parsedCommand.method = methodMatch[1].toUpperCase();
          }

          // Extract URL - try multiple patterns for URL extraction
          
          // Pattern 1: URL in quotes after curl
          let urlMatch = curlCommand.match(/curl\s+['"]?(https?:\/\/[^"'\s]+)['"]?/i);
          
          // Pattern 2: URL in quotes anywhere
          if (!urlMatch) {
            urlMatch = curlCommand.match(/['"]?(https?:\/\/[^"'\s]+)['"]?/i);
          }
          
          // Pattern 3: URL after single quotes
          if (!urlMatch) {
            urlMatch = curlCommand.match(/curl\s+'([^']+)'/i);
          }
          
          if (urlMatch && urlMatch[1]) {
            parsedCommand.url = urlMatch[1].replace(/["']/g, '');
          }

          // Extract headers (-H or --header)
          const headerRegex = /(?:\s-H|\s--header)\s+["']([^:]+):\s*([^"']+)["']/g;
          let headerMatch;
          while ((headerMatch = headerRegex.exec(curlCommand)) !== null) {
            if (headerMatch[1] && headerMatch[2]) {
              parsedCommand.headers[headerMatch[1].trim()] = headerMatch[2].trim();
            }
          }

          // Extract body (-d, --data, --data-binary, --data-raw)
          const bodyRegex = /(?:\s-d|\s--data|\s--data-binary|\s--data-raw)\s+["'](.+?)["'](?:\s|$)/s;
          const bodyMatch = curlCommand.match(bodyRegex);
          if (bodyMatch && bodyMatch[1]) {
            let bodyContent = bodyMatch[1];
            
            // Try to parse as JSON
            try {
              // If it's escaped JSON, unescape it
              if (bodyContent.includes('\\\"')) {
                bodyContent = bodyContent.replace(/\\"/g, '"');
              }
              
              // Parse and re-stringify to format properly
              const parsedJson = JSON.parse(bodyContent);
              parsedCommand.body = JSON.stringify(parsedJson, null, 2);
            } catch (e) {
              // If not valid JSON, use as is
              parsedCommand.body = bodyContent;
            }
          }
          
          // If no body found yet, try alternate formats
          if (!parsedCommand.body) {
            // For unquoted data
            const altBodyRegex = /(?:\s-d|\s--data|\s--data-binary|\s--data-raw)\s+(\S+)(?:\s|$)/;
            const altBodyMatch = curlCommand.match(altBodyRegex);
            if (altBodyMatch && altBodyMatch[1]) {
              let bodyContent = altBodyMatch[1];
              try {
                const parsedJson = JSON.parse(bodyContent);
                parsedCommand.body = JSON.stringify(parsedJson, null, 2);
              } catch (e) {
                parsedCommand.body = bodyContent;
              }
            }
          }
          
          // Last resort body extraction - look for data content at the end
          if (!parsedCommand.body) {
            // Try to find --data-raw at the end of the command
            const endBodyMatch = curlCommand.match(/--data-raw\s+['"](.+?)['"]$/);
            if (endBodyMatch && endBodyMatch[1]) {
              parsedCommand.body = endBodyMatch[1];
            }
          }
          
          // For Telkomsel-like curl commands with special escaping
          if (!parsedCommand.body && curlCommand.includes('--data-raw')) {
            const rawDataMatch = curlCommand.match(/--data-raw\s+'([^']+)'/);
            if (rawDataMatch && rawDataMatch[1]) {
              try {
                const parsedJson = JSON.parse(rawDataMatch[1]);
                parsedCommand.body = JSON.stringify(parsedJson, null, 2);
              } catch (e) {
                parsedCommand.body = rawDataMatch[1];
              }
            }
          }

          // Update the form fields
          this.value = parsedCommand.url;
          document.getElementById('method').value = parsedCommand.method;

          // Set headers
          if (Object.keys(parsedCommand.headers).length > 0) {
            headersEditor.setValue(JSON.stringify(parsedCommand.headers, null, 2));
          }

          // Set body if present and method allows it
          if (parsedCommand.body && ["POST", "PUT", "PATCH"].includes(parsedCommand.method)) {
            bodyEditor.setValue(parsedCommand.body);
            bodyEditor.setOption('readOnly', false);
            bodyEditor.getWrapperElement().style.opacity = '1';
          }

          // Trigger the method change to update form state
          const event = new Event('change');
          document.getElementById('method').dispatchEvent(event);
        } catch (error) {
          console.error('Error parsing curl command:', error);
          alert('Failed to parse curl command: ' + error.message);
        }
      }
    });
  });
</script>
@endpush
