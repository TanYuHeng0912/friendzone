@extends('layouts.adminapp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-edit"></i> Edit Community: {{ $community->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.communities.update', $community) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group">
                            <label for="name">Community Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $community->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required>{{ old('description', $community->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="icon">Community Icon</label>
                            @if($community->icon)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $community->icon) }}" alt="Current Icon" class="img-thumbnail" width="100">
                                    <p class="text-muted">Current icon</p>
                                </div>
                            @endif
                            <input type="file" class="form-control-file @error('icon') is-invalid @enderror" 
                                   id="icon" name="icon" accept="image/*">
                            <small class="form-text text-muted">Upload a new icon to replace the current one (optional). Max size: 2MB</small>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Community Stats:</strong>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-users"></i> {{ $community->members_count }} members</li>
                                        <li><i class="fas fa-posts"></i> {{ $community->posts_count }} posts</li>
                                        <li><i class="fas fa-calendar"></i> Created {{ $community->created_at->format('M d, Y') }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <strong>Current Slug:</strong>
                                    <p class="text-muted">{{ $community->slug }}</p>
                                    <small class="text-info">Slug will be automatically updated based on the name</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group d-flex justify-content-between">
                            <a href="{{ route('admin.communities') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Communities
                            </a>
                            <div>
                                <a href="{{ route('community.show', $community) }}" class="btn btn-info mr-2" target="_blank">
                                    <i class="fas fa-eye"></i> View Community
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Update Community
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection