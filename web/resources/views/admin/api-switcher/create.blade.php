@extends('layouts.main')

@section('title', 'Create API Criteria')

@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Create New API Criteria</h3>
  </div>
  <div class="card-body">
    @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form action="{{ route('api-switcher.store.ui') }}" method="POST">
      @csrf
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="name">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="E.g. Get Users List">
          </div>
          
          <div class="form-group mt-3">
            <label for="path">API Path <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">{{ rtrim(parse_url(config('app.url'))['path'], '/') }}/api/</span>
              </div>
              <input type="text" class="form-control" id="path" name="path" value="{{ old('path') }}" required placeholder="E.g. users">
            </div>
            <small class="form-text text-muted">Do not include leading slash</small>
          </div>
          
          <div class="form-group mt-3">
            <label for="method">HTTP Method <span class="text-danger">*</span></label>
            <select class="form-control" id="method" name="method" required>
              <option value="GET" {{ old('method') == 'GET' ? 'selected' : '' }}>GET</option>
              <option value="POST" {{ old('method') == 'POST' ? 'selected' : '' }}>POST</option>
              <option value="PUT" {{ old('method') == 'PUT' ? 'selected' : '' }}>PUT</option>
              <option value="PATCH" {{ old('method') == 'PATCH' ? 'selected' : '' }}>PATCH</option>
              <option value="DELETE" {{ old('method') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
              <option value="OPTIONS" {{ old('method') == 'OPTIONS' ? 'selected' : '' }}>OPTIONS</option>
            </select>
          </div>
          
          <div class="form-group mt-3">
            <label for="type">API Type <span class="text-danger">*</span></label>
            <select class="form-control" id="type" name="type" required onchange="toggleBodySection()">
              <option value="real" {{ old('type') == 'real' ? 'selected' : '' }}>Real API (Forward Request)</option>
              <option value="mock" {{ old('type') == 'mock' ? 'selected' : '' }}>Mock Response</option>
            </select>
          </div>
          
          <div class="form-group mt-3">
            <label for="status_code">Status Code <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="status_code" name="status_code" value="{{ old('status_code', 200) }}" required min="100" max="599">
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            <label for="content_type">Content Type</label>
            <input type="text" class="form-control" id="content_type" name="content_type" value="{{ old('content_type', 'application/json') }}">
          </div>
          
          <div class="form-group mt-3">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="2">{{ old('description') }}</textarea>
          </div>
          
          <div class="form-group mt-3" id="mockBodySection">
            <label for="body">Response Body (for Mock APIs)</label>
            <textarea class="form-control" id="body" name="body" rows="9" style="font-family: monospace;">{{ old('body', json_encode(array("status"=>"success","data"=>[]))) }}</textarea>
            <small class="form-text text-muted">Enter valid JSON data</small>
          </div>
          
          <div class="form-group mt-3" id="realApiUrlSection">
            <label for="real_api_url">Custom Real API URL <span class="text-muted" style="font-weight:normal;">(optional)</span></label>
            <input type="url" class="form-control" id="real_api_url" name="real_api_url" value="{{ old('real_api_url') }}" placeholder="https://external.api/endpoint">
            <small class="form-text text-muted">Override the global real API base URL for this criteria only. Leave blank to use default.</small>
          </div>
          
          <div class="form-check mt-4">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
          </div>
        </div>
      </div>
      
      <div class="form-group mt-4">
        <button type="submit" class="btn btn-primary">Save API Criteria</button>
        <a href="{{ route('api-switcher.dashboard') }}" class="btn btn-secondary ml-2">Cancel</a>
      </div>
    </form>
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
  let bodyEditor = null;
  function toggleBodySection() {
    const type = document.getElementById('type').value;
    const mockBodySection = document.getElementById('mockBodySection');
    const realApiUrlSection = document.getElementById('realApiUrlSection');
    if (type === 'real') {
      mockBodySection.style.display = 'none';
      realApiUrlSection.style.display = 'block';
      if (bodyEditor) bodyEditor.getWrapperElement().style.display = 'none';
    } else {
      mockBodySection.style.display = 'block';
      realApiUrlSection.style.display = 'none';
      if (bodyEditor) bodyEditor.getWrapperElement().style.display = 'block';
    }
  }
  document.addEventListener('DOMContentLoaded', function() {
    toggleBodySection();
    var headersTextarea = document.getElementById('headers');
    var bodyTextarea = document.getElementById('body');
    if (bodyTextarea) {
      if (!bodyEditor) {
        bodyEditor = CodeMirror.fromTextArea(bodyTextarea, {
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
      }
      // Sync CodeMirror content to textarea before submit
      document.querySelector('form').addEventListener('submit', function(e) {
        bodyEditor.save();
        const type = document.getElementById('type').value;
        if (type !== 'real' && bodyTextarea.value.trim() !== '') {
          try {
            JSON.parse(bodyTextarea.value);
          } catch (error) {
            e.preventDefault();
            alert('Invalid JSON in Response Body: ' + error.message);
            bodyEditor.focus();
          }
        }
      });
      // Hide/show CodeMirror based on type
      document.getElementById('type').addEventListener('change', function() {
        toggleBodySection();
      });
      // Initial display
      if (document.getElementById('type').value === 'real') {
        bodyEditor.getWrapperElement().style.display = 'none';
      } else {
        bodyEditor.getWrapperElement().style.display = 'block';
      }
    }
  });
</script>
@endpush