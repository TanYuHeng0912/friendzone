@extends('layouts.adminapp')

@section('page-title', 'Edit Community')
@section('page-subtitle', 'Update community information')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="stat-card">
            <h4 class="mb-4"><i class="fas fa-edit"></i> Edit Community: {{ $community->name }}</h4>
            
            <form action="{{ route('admin.communities.update', $community) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Community Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $community->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="5" required>{{ old('description', $community->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="icon" class="form-label">Community Icon</label>
                    @if($community->icon)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $community->icon) }}" 
                                 alt="Current Icon" 
                                 style="width: 100px; height: 100px; border-radius: 10px; object-fit: cover; border: 2px solid #e9ecef;">
                            <p class="text-muted mt-2">Current icon</p>
                        </div>
                    @endif
                    <input type="file" class="form-control @error('icon') is-invalid @enderror" 
                           id="icon" name="icon" accept="image/*">
                    <small class="form-text text-muted">Upload a new icon to replace the current one (optional). Max size: 2MB</small>
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Community Stats:</strong>
                        <ul class="list-unstyled mt-2">
                            <li><i class="fas fa-users text-primary"></i> {{ $community->members_count }} members</li>
                            <li><i class="fas fa-file-alt text-info"></i> {{ $community->posts_count }} posts</li>
                            <li><i class="fas fa-calendar text-success"></i> Created {{ $community->created_at->format('M d, Y') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>Current Slug:</strong>
                        <p class="text-muted">{{ $community->slug }}</p>
                        <small class="text-info"><i class="fas fa-info-circle"></i> Slug will be automatically updated based on the name</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.communities') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Communities
                    </a>
                    <div>
                        <a href="{{ route('community.show', $community) }}" class="btn btn-info" target="_blank">
                            <i class="fas fa-eye"></i> View Community
                        </a>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save"></i> Update Community
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
