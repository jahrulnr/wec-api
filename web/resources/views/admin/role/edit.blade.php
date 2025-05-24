@extends('layouts.main')

@section('title', 'Edit Role')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('roles.show', $role->id) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> View Details
        </a>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Roles
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
        <h3 class="card-title">Edit Role Information</h3>
    </div>
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card-body">
            <div class="form-group mb-3">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                    id="name" name="name" value="{{ old('name', $role->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="description">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                    id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-4">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                        {{ $role->is_active ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Manage Permissions</label>
                <div class="card">
                    <div class="card-body">
                        @if($permissionGroups->count() > 0)
                            <div class="accordion" id="permissionAccordion">
                                @foreach($permissionGroups as $group => $permissions)
                                    <div class="card mb-1">
                                        <div class="card-header p-0" id="heading{{ Str::slug($group) }}">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" 
                                                    data-target="#collapse{{ Str::slug($group) }}" aria-expanded="false" 
                                                    aria-controls="collapse{{ Str::slug($group) }}">
                                                    {{ $group ?? 'General' }}
                                                    <span class="badge bg-primary float-right">{{ $permissions->count() }}</span>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapse{{ Str::slug($group) }}" class="collapse" 
                                            aria-labelledby="heading{{ Str::slug($group) }}" data-parent="#permissionAccordion">
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($permissions as $permission)
                                                        <div class="col-md-4">
                                                            <div class="custom-control custom-checkbox mb-2">
                                                                <input type="checkbox" class="custom-control-input" 
                                                                    id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}"
                                                                    {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                                                    {{ $permission->name }}
                                                                </label>
                                                                @if($permission->description)
                                                                    <small class="form-text text-muted d-block">{{ $permission->description }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No permissions available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Role
            </button>
        </div>
    </form>
</div>
@endsection
