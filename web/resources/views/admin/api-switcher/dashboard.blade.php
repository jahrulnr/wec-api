@extends('layouts.main')

@section('title', 'API Switcher')
@section('menu_card')
<!-- Optionally, add a summary card here -->
@endsection

@section('content')
<div class="mb-3">
    @if(auth()->user()->hasPermission('api-switcher-manage'))
        <a href="{{ route('api-switcher.create') }}" class="btn btn-primary">Create New Criteria</a>
    @endif
    <a href="{{ route('api-switcher.test') }}" class="btn btn-secondary">Test API Endpoint</a>
    <a href="{{ route('api-switcher.logs') }}" class="btn btn-info">View Logs</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">API Criteria List</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Path</th>
                    <th>Method</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Real API URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($criteria as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->path }}</td>
                    <td>{{ $c->method }}</td>
                    <td>{{ $c->type }}</td>
                    <td>
                        @if($c->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $c->description }}</td>
                    <td>
                        @if($c->real_api_url)
                            <a href="{{ $c->real_api_url }}" target="_blank" rel="noopener" title="Custom real API endpoint">
                                {{ Str::limit($c->real_api_url, 40) }}
                                <i class="fas fa-external-link-alt" style="font-size:0.9em;"></i>
                            </a>
                        @else
                            <span class="text-muted" title="Uses global base URL">(default)</span>
                        @endif
                    </td>
                    <td>
                        @if(auth()->user()->hasPermission('api-switcher-manage'))
                        <div class="btn-group">
                            <a href="{{ route('api-switcher.edit', $c->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('api-switcher.delete', $c->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this criteria?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <form action="{{ route('api-switcher.toggle.ui', $c->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-{{ $c->is_active ? 'success' : 'secondary' }}">
                                    <i class="fas fa-{{ $c->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-muted">No Access</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center">No criteria found.</td></tr>
            @endforelse
            </tbody>
        </table>
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
  // CodeMirror for all textareas
  document.querySelectorAll('textarea').forEach(function(textarea) {
    var mode = 'javascript';
    if (textarea.name === 'description') mode = 'text';
    var editor = CodeMirror.fromTextArea(textarea, {
      mode: mode,
      theme: "material-darker",
      lineNumbers: true,
      lineWrapping: true,
      matchBrackets: true,
      autoCloseBrackets: true,
      tabSize: 2,
      viewportMargin: Infinity,
      height: 'auto',
    });
    document.querySelector('form')?.addEventListener('submit', function() {
      editor.save();
    });
  });
  // CodeMirror for all <pre> tags (read-only, beautify JSON if possible)
  document.querySelectorAll('pre').forEach(function(pre) {
    var code = pre.textContent;
    var mode = 'javascript';
    try {
      var json = JSON.parse(code);
      code = JSON.stringify(json, null, 2);
      mode = {name: 'javascript', json: true};
    } catch (e) {
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
