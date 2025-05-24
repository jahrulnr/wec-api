@extends('layouts.main')

@section('title', 'Edit User')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
  <div>
		<a href="{{ route('users.show', $user->id) }}" class="btn btn-info">
			<i class="fas fa-eye"></i> View Details
		</a>
		<a href="{{ route('users.index') }}" class="btn btn-secondary">
			<i class="fas fa-arrow-left"></i> Back to Users
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
        <h3 class="card-title">Edit User Information</h3>
    </div>
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card-body">
            <div class="form-group mb-3">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="password">Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                    id="password" name="password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" class="form-control" 
                    id="password_confirmation" name="password_confirmation">
            </div>
            
            <div class="form-group mb-3">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                        {{ $user->is_active ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Assign Roles</label>
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-4">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" 
                                    id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}"
                                    {{ in_array($role->id, old('roles', $userRoleIds)) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="role_{{ $role->id }}">
                                    {{ $role->name }}
                                </label>
                                @if($role->description)
                                    <small class="form-text text-muted d-block">{{ $role->description }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update User
            </button>
        </div>
    </form>
</div>
@endsection
