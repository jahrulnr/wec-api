@extends('layouts.main')

@section('title', 'Permission Details')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        @if(auth()->user()->hasPermission('permissions-edit'))
        <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        @endif
        
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Permissions
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Permission Information</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 30%">ID</th>
                        <td>{{ $permission->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $permission->name }}</td>
                    </tr>
                    <tr>
                        <th>Slug</th>
                        <td><code>{{ $permission->slug }}</code></td>
                    </tr>
                    <tr>
                        <th>Group</th>
                        <td>
                            @if($permission->group)
                                <span class="badge bg-info">{{ $permission->group }}</span>
                            @else
                                <span class="badge bg-secondary">General</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $permission->description ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $permission->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $permission->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Roles with this Permission</h3>
            </div>
            <div class="card-body">
                @if($permission->roles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permission->roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->description ?? 'N/A' }}</td>
                                        <td>
                                            @if($role->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('roles.show', $role->id) }}" class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No roles have been assigned this permission.</p>
                @endif
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Usage Information</h3>
            </div>
            <div class="card-body">
                <p><strong>Permission Check Code:</strong></p>
                <pre><code>// In Blade templates:
@if(auth()->user()->hasPermission('{{ $permission->slug }}'))
    <!-- Protected content here -->
@endif

// In Controllers:
if ($request->user()->hasPermission('{{ $permission->slug }}')) {
    // Allow access
}

// In Route definitions:
Route::middleware('permission:{{ $permission->slug }}')->group(function() {
    // Protected routes
});</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection
