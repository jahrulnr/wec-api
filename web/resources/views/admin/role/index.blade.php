@extends('layouts.main')

@section('title', 'Roles')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    @if(auth()->user()->hasPermission('roles-create'))
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Role
    </a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Role List</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Users</th>
                        <th>Permissions</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->description ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $role->users_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $role->permissions_count }}</span>
                            </td>
                            <td>
                                @if($role->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('roles.show', $role->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(auth()->user()->hasPermission('roles-edit'))
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    @if(auth()->user()->hasPermission('roles-delete'))
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this role? This will remove the role from all assigned users.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No roles found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $roles->links() }}
    </div>
</div>
@endsection
