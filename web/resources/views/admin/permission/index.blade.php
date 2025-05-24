@extends('layouts.main')

@section('title', 'Permissions')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    @if(auth()->user()->hasPermission('permissions-create'))
    <a href="{{ route('permissions.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Permission
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
        <h3 class="card-title">Permission List</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Group</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td>{{ $permission->name }}</td>
                            <td><code>{{ $permission->slug }}</code></td>
                            <td>
                                @if($permission->group)
                                    <span class="badge bg-info">{{ $permission->group }}</span>
                                @else
                                    <span class="badge bg-secondary">General</span>
                                @endif
                            </td>
                            <td>{{ $permission->description ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(auth()->user()->hasPermission('permissions-edit'))
                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    @if(auth()->user()->hasPermission('permissions-delete'))
                                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this permission? This will remove the permission from all roles.');">
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
                            <td colspan="6" class="text-center">No permissions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $permissions->links() }}
    </div>
</div>
@endsection
