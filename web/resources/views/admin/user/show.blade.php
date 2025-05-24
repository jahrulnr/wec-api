@extends('layouts.main')

@section('title', 'User Details')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        @if(auth()->user()->hasPermission('users-edit'))
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        @endif
        
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Users
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
                <h3 class="card-title">User Information</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 30%">ID</th>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $user->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Assigned Roles</h3>
            </div>
            <div class="card-body">
                @if($user->roles->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->roles as $role)
                                    <tr>
                                        <td>
                                            <span class="badge bg-info">{{ $role->name }}</span>
                                        </td>
                                        <td>{{ $role->description ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No roles assigned to this user.</p>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Permissions</h3>
            </div>
            <div class="card-body">
                @php
                    $allPermissions = collect();
                    foreach($user->roles as $role) {
                        $allPermissions = $allPermissions->merge($role->permissions);
                    }
                    $uniquePermissions = $allPermissions->unique('id')->groupBy('group');
                @endphp
                
                @if($uniquePermissions->count() > 0)
                    <div class="accordion" id="permissionsAccordion">
                        @foreach($uniquePermissions as $group => $permissions)
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
                                        <div class="row">
                                            @foreach($permissions as $permission)
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <span class="badge bg-secondary">{{ $permission->name }}</span>
                                                        @if($permission->description)
                                                            <small class="d-block text-muted">{{ $permission->description }}</small>
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
                    <p class="text-muted">No permissions available for this user.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
