@extends('layouts.main')

@section('title', 'API Test Result')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex align-items-center justify-content-between">
      <h3 class="card-title mb-0">API Test Result</h3>
      <a href="{{ route('api-switcher.test') }}" class="btn btn-secondary">Back to Test</a>
    </div>
  </div>
  <div class="card-body">
    @if(isset($result))
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="mb-2">
            <span class="h6">Status:</span>
            <span class="badge {{ $result['status_code'] < 400 ? 'bg-success' : 'bg-danger' }} text-white align-middle" style="font-size:1.1em;">
              {{ $result['status_code'] }} {{ $result['status_text'] ?? '' }}
            </span>
          </div>
          <div class="mb-2">
            <span class="h6">Response Time:</span> <strong>{{ $result['time'] ?? '-' }} ms</strong>
          </div>
          <div class="mb-2">
            <span class="h6">Mode:</span> <span class="badge {{ $result['mode'] === 'real' ? 'bg-primary' : 'bg-warning' }} text-white align-middle">{{ strtoupper($result['mode']) }}</span>
          </div>
        </div>
        <div class="col-md-8 d-flex align-items-end">
          <div class="mb-2">
            <span class="h6">Request:</span>
            <span class="ml-2"><strong>{{ $result['request']['method'] ?? '-' }}</strong> <code class="text-primary">{{ $result['request']['path'] ?? '-' }}</code></span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="card mb-3 shadow-sm border border-primary">
            <div class="card-header py-2 px-3 bg-primary text-white border-bottom">
              <strong>Request</strong>
            </div>
            <div class="card-body p-3">
              <div class="form-group mb-2">
                <label class="mb-1">Headers</label>
                <pre class="bg-light p-2 rounded border small">{{ json_encode($result['request']['headers'] ?? [], JSON_PRETTY_PRINT) }}</pre>
              </div>
              <div class="form-group mb-0">
                <label class="mb-1">Body</label>
                <pre class="bg-light p-2 rounded border small" style="min-height: 40px;">{{ $result['request']['body'] ?? '{}' }}</pre>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card mb-3 shadow-sm border border-success">
            <div class="card-header py-2 px-3 bg-success text-white border-bottom">
              <strong>Response</strong>
            </div>
            <div class="card-body p-3">
              <div class="form-group mb-2">
                <label class="mb-1">Headers</label>
                <pre class="bg-light p-2 rounded border small">{{ json_encode($result['headers'] ?? [], JSON_PRETTY_PRINT) }}</pre>
              </div>
              <div class="form-group mb-0">
                <label class="mb-1">Body</label>
                <pre class="bg-light p-2 rounded border small" style="max-height: 300px; overflow-y: auto; white-space: pre-wrap; min-height: 40px;">{{ $result['body'] ?? '' }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    @else
      <div class="alert alert-warning">No test result found.</div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
  // CodeMirror for all <pre> tags (read-only, beautify JSON if possible)
  document.querySelectorAll('pre').forEach(function(pre) {
    var code = pre.textContent;
    var mode = 'javascript';
    // Try to pretty-print JSON, but only if it looks like JSON
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
