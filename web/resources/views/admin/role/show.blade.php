@extends('layouts.main')

@section('title', 'Role Details')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        @if(auth()->user()->hasPermission('roles-edit'))
        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        @endif
        
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Role Information</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 30%">ID</th>
                        <td>{{ $role->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $role->name }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $role->description ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($role->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Users</th>
                        <td>
                            <span class="badge bg-primary">{{ $role->users_count }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $role->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $role->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Assigned Permissions</h3>
            </div>
            <div class="card-body">
                @if($role->permissions->count() > 0)
                    @php
                        $permissionGroups = $role->permissions->groupBy('group');
                    @endphp
                    
                    <div class="accordion" id="permissionsAccordion">
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
                                    aria-labelledby="heading{{ Str::slug($group) }}" data-parent="#permissionsAccordion">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Slug</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($permissions as $permission)
                                                        <tr>
                                                            <td>{{ $permission->name }}</td>
                                                            <td><code>{{ $permission->slug }}</code></td>
                                                            <td>{{ $permission->description ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No permissions assigned to this role.</p>
                @endif
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Users with this Role</h3>
            </div>
            <div class="card-body">
                @if($role->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No users have been assigned this role.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
