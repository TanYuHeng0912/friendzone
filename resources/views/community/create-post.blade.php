@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="create-post-header">
                <div class="d-flex align-items-center mb-3">
                    <a href="{{ route('community.show', $community) }}" class="btn btn-outline-secondary mr-3">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <div>
                        <h2 class="mb-0">Create New Post</h2>
                        <small class="text-muted">in {{ $community->name }} {{ $community->icon }}</small>
                    </div>
                </div>
            </div>

            <div class="create-post-card">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('community.store-post', $community) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label for="title" class="form-label">Post Title</label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               placeholder="Enter an engaging title for your post"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" 
                                  name="content" 
                                  rows="8" 
                                  placeholder="Share your thoughts, experiences, or questions with the community..."
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label">Image (Optional)</label>
                        <div class="custom-file">
                            <input type="file" 
                                   class="custom-file-input @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            <label class="custom-file-label" for="image">Choose image...</label>
                        </div>
                        <small class="form-text text-muted">
                            Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB
                        </small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="image-preview" id="imagePreview" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" class="img-fluid">
                        <button type="button" class="btn btn-sm btn-danger remove-image" onclick="removeImage()">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane"></i> Publish Post
                        </button>
                        <a href="{{ route('community.show', $community) }}" class="btn btn-secondary btn-lg ml-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.create-post-header {
    margin-bottom: 20px;
}

.create-post-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.form-control {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.custom-file-label {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
}

.image-preview {
    margin-top: 15px;
    position: relative;
    display: inline-block;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.image-preview img {
    max-width: 300px;
    max-height: 200px;
    border-radius: 10px;
}

.remove-image {
    position: absolute;
    top: 10px;
    right: 10px;
    border-radius: 50%;
}

.form-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.btn-lg {
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
}

.btn-secondary {
    background: #6c757d;
    border: none;
}

.alert {
    border-radius: 10px;
    border: none;
    margin-bottom: 20px;
}

.invalid-feedback {
    font-size: 14px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle file input change
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const label = document.querySelector('.custom-file-label');
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        if (file) {
            label.textContent = file.name;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            label.textContent = 'Choose image...';
            preview.style.display = 'none';
        }
    });
});

function removeImage() {
    const fileInput = document.getElementById('image');
    const label = document.querySelector('.custom-file-label');
    const preview = document.getElementById('imagePreview');
    
    fileInput.value = '';
    label.textContent = 'Choose image...';
    preview.style.display = 'none';
}
</script>
@endsection