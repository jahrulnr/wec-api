@extends('layouts.main')

@section('title', 'Edit Permission')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> View Details
        </a>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Permissions
        </a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Permission Information</h3>
    </div>
    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card-body">
            <div class="form-group mb-3">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                    id="name" name="name" value="{{ old('name', $permission->name) }}" required>
                <small class="form-text text-muted">
                    The human-readable name of the permission (e.g., "View Users").
                </small>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="slug">Slug</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                    id="slug" name="slug" value="{{ old('slug', $permission->slug) }}">
                <small class="form-text text-muted">
                    The system identifier for the permission. Changing this may break existing permission checks.
                </small>
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="description">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                    id="description" name="description" rows="3">{{ old('description', $permission->description) }}</textarea>
                <small class="form-text text-muted">
                    A brief description of what this permission allows.
                </small>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="group">Group</label>
                <div class="input-group">
                    <select class="form-control @error('group') is-invalid @enderror" id="group" name="group">
                        <option value="">-- Select Group --</option>
                        @foreach($permissionGroups as $group)
                            <option value="{{ $group }}" {{ old('group', $permission->group) == $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="showNewGroupInput">
                            <i class="fas fa-plus"></i> New Group
                        </button>
                    </div>
                </div>
                <div id="newGroupInput" class="mt-2" style="display: none;">
                    <input type="text" class="form-control" id="newGroup" placeholder="Enter new group name">
                    <small class="form-text text-muted">
                        Enter a new group name and click "Use This Group" to add it.
                    </small>
                    <button type="button" class="btn btn-sm btn-primary mt-1" id="useNewGroup">
                        <i class="fas fa-check"></i> Use This Group
                    </button>
                </div>
                <small class="form-text text-muted">
                    Group to categorize this permission (e.g., "Users", "Roles", "Settings").
                </small>
                @error('group')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Permission
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle showing/hiding the new group input
        document.getElementById('showNewGroupInput').addEventListener('click', function() {
            document.getElementById('newGroupInput').style.display = 'block';
        });
        
        // Handle using the new group
        document.getElementById('useNewGroup').addEventListener('click', function() {
            const newGroupValue = document.getElementById('newGroup').value.trim();
            if (newGroupValue) {
                // Create a new option
                const option = document.createElement('option');
                option.value = newGroupValue;
                option.text = newGroupValue;
                option.selected = true;
                
                // Add to select and hide the input
                document.getElementById('group').appendChild(option);
                document.getElementById('newGroupInput').style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection
