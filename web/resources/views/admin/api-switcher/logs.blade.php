@extends('layouts.main')

@section('title', 'API Switcher Logs')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between">
      <h3 class="card-title">API Request Logs</h3>
      <div>
        <form action="{{ route('api-switcher.logs') }}" method="GET" class="form-inline">
          <div class="input-group">
            <select class="form-control form-control-sm" name="filter">
              <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All</option>
              <option value="real" {{ request('filter') == 'real' ? 'selected' : '' }}>Real API</option>
              <option value="mock" {{ request('filter') == 'mock' ? 'selected' : '' }}>Mock API</option>
              <option value="error" {{ request('filter') == 'error' ? 'selected' : '' }}>Errors Only</option>
            </select>
            <div class="input-group-append">
              <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="card-header bg-light">
    <div class="row">
      <div class="col-md-3">
        <div class="info-box bg-info">
          <div class="info-box-content">
            <span class="info-box-text">Total Logs</span>
            <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="info-box bg-success">
          <div class="info-box-content">
            <span class="info-box-text">Requests/Responses</span>
            <span class="info-box-number">{{ $stats['requests'] ?? 0 }}/{{ $stats['responses'] ?? 0 }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="info-box bg-warning">
          <div class="info-box-content">
            <span class="info-box-text">Mock/Real</span>
            <span class="info-box-number">{{ $stats['mock'] ?? 0 }}/{{ $stats['real'] ?? 0 }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="info-box bg-danger">
          <div class="info-box-content">
            <span class="info-box-text">Errors</span>
            <span class="info-box-number">{{ $stats['errors'] ?? 0 }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="mb-3">
      <form action="{{ route('api-switcher.logs.clear') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear all logs?');">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm mt-2 ml-3">Clear All Logs</button>
      </form>
    </div>
    
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Time</th>
            <th>Type</th>
            <th>Method</th>
            <th>Path</th>
            <th>Status</th>
            <th>Mode</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs ?? [] as $log)
          <tr class="{{ $log->status_code >= 400 ? 'table-danger' : '' }}">
            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
            <td><span class="badge bg-{{ $log->type === 'request' ? 'primary' : ($log->type === 'response' ? 'success' : 'danger') }}">{{ $log->type }}</span></td>
            <td>{{ $log->method }}</td>
            <td>{{ $log->path }}</td>
            <td>
              @if($log->status_code)
                <span class="badge bg-{{ $log->status_code < 400 ? 'success' : 'danger' }}">{{ $log->status_code }}</span>
              @else
                -
              @endif
            </td>
            <td>
              @if($log->response_type)
                <span class="badge bg-{{ $log->response_type === 'real' ? 'primary' : 'warning' }}">
                {{ strtoupper($log->response_type) }}
                </span>
              @else
                -
              @endif  
            </td>
            <td>
              <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#logModal-{{ $loop->index }}">
                Details
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-3">No logs found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Log Detail Modals -->
@foreach($logs ?? [] as $index => $log)
<div class="modal fade" id="logModal-{{ $index }}" role="dialog" aria-labelledby="logModalLabel-{{ $index }}" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logModalLabel-{{ $index }}">{{ $log->method ?? 'GET' }} {{ $log->path ?? '/' }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="logTab-{{ $index }}" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="request-tab-{{ $index }}" data-toggle="tab" href="#request-{{ $index }}" role="tab" aria-controls="request" aria-selected="true">Request</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="response-tab-{{ $index }}" data-toggle="tab" href="#response-{{ $index }}" role="tab" aria-controls="response" aria-selected="false">Response</a>
          </li>
        </ul>
        <div class="tab-content pt-3" id="logTabContent-{{ $index }}">
          <div class="tab-pane fade show active" id="request-{{ $index }}" role="tabpanel" aria-labelledby="request-tab-{{ $index }}">
            <div class="form-group">
              <label>IP Address:</label>
              <pre class="bg-light p-2">{{ $log->ip ?? 'N/A' }}</pre>
            </div>
            <div class="form-group">
              <label>Body:</label>
              <pre class="bg-light p-2" style="max-height: 200px; overflow-y: auto;">{{ $log->request_body ? json_encode($log->request_body, JSON_PRETTY_PRINT) : 'N/A' }}</pre>
            </div>
          </div>
          <div class="tab-pane fade" id="response-{{ $index }}" role="tabpanel" aria-labelledby="response-tab-{{ $index }}">
            <div class="form-group">
              <label>Status Code:</label>
              <pre class="bg-light p-2">{{ $log->status_code ?? 'N/A' }}</pre>
            </div>
            <div class="form-group">
              <label>Response Type:</label>
              <pre class="bg-light p-2">{{ strtoupper($log->response_type ?? 'N/A') }}</pre>
            </div>
            <div class="form-group">
              <label>Body:</label>
              <pre class="bg-light p-2" style="max-height: 200px; overflow-y: auto; white-space: pre-wrap;">{{ $log->response_body ? json_encode($log->response_body, JSON_PRETTY_PRINT) : 'N/A' }}</pre>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <a href="{{ route('api-switcher.create') }}?path={{ $log->path ?? '' }}&method={{ $log->method ?? 'GET' }}" class="btn btn-primary">
          Create API Criteria
        </a>
      </div>
    </div>
  </div>
</div>
@endforeach

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
function beautifyPreTags(context) {
  (context || document).querySelectorAll('pre').forEach(function(pre) {
    // Always replace <pre> with a new CodeMirror instance to avoid double line numbers
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
}
document.addEventListener('DOMContentLoaded', function() {
  beautifyPreTags();
  // Re-initialize CodeMirror for <pre> tags inside modals when shown
  document.querySelectorAll('.modal').forEach(function(modal) {
    modal.addEventListener('shown.bs.modal', function() {
      beautifyPreTags(modal);
    });
  });
});
</script>
@endpush

@endsection