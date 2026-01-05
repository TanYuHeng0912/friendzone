@extends('layouts.adminapp')

@section('page-title', 'Create Community')
@section('page-subtitle', 'Add a new community')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="stat-card">
            <h4 class="mb-4"><i class="fas fa-plus"></i> Create New Community</h4>
            
            <form action="{{ route('admin.communities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Community Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="icon" class="form-label">Community Icon</label>
                    <input type="file" class="form-control @error('icon') is-invalid @enderror" 
                           id="icon" name="icon" accept="image/*">
                    <small class="form-text text-muted">Upload an icon for the community (optional). Max size: 2MB</small>
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.communities') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Communities
                    </a>
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-save"></i> Create Community
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
