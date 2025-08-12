@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Community Header -->
    <div class="community-header-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="community-icon-large">{{ $community->icon }}</div>
                    <div class="ml-3">
                        <h1 class="community-title">{{ $community->name }}</h1>
                        <p class="community-description">{{ $community->description }}</p>
                        <div class="community-meta">
                            <span class="badge badge-info mr-2">
                                <i class="fas fa-users"></i> {{ $community->members_count }} members
                            </span>
                            <span class="badge badge-secondary">
                                <i class="fas fa-newspaper"></i> {{ $community->posts_count }} posts
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('community.create-post', $community) }}" class="btn btn-success btn-lg">
                    <i class="fas fa-plus"></i> Create Post
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Posts Section -->
    <div class="posts-section">
        @if($posts->count() > 0)
            @foreach($posts as $post)
                <div class="post-card">
                    <div class="post-header">
                        <div class="d-flex align-items-center">
                            <img src="{{ $post->user->info->getPicture() }}" 
                                 alt="User avatar" class="user-avatar">
                            <div class="ml-3">
                                <h6 class="user-name mb-0">{{ $post->user->info->name }} {{ $post->user->info->surname }}</h6>
                                <small class="text-muted">{{ $post->time_ago }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="post-content">
                        <h4 class="post-title">
                            <a href="{{ route('community.post', [$community, $post]) }}">{{ $post->title }}</a>
                        </h4>
                        <p class="post-excerpt">{{ Str::limit($post->content, 200) }}</p>
                        
                        @if($post->image)
                            <div class="post-image">
                                <img src="{{ $post->getImageUrl() }}" alt="Post image" class="img-fluid">
                            </div>
                        @endif
                    </div>

                    <div class="post-footer">
                        <div class="post-actions">
                            <button class="action-btn like-btn" data-post-id="{{ $post->id }}">
                                <i class="fas fa-heart {{ $post->isLikedBy(auth()->id()) ? 'text-danger' : '' }}"></i>
                                <span class="likes-count">{{ $post->likes_count }}</span>
                            </button>
                            <a href="{{ route('community.post', [$community, $post]) }}" class="action-btn">
                                <i class="fas fa-comment"></i>
                                {{ $post->comments_count }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="text-center">
                    <i class="fas fa-newspaper empty-icon"></i>
                    <h3>No posts yet!</h3>
                    <p>Be the first to share something with this community.</p>
                    <a href="{{ route('community.create-post', $community) }}" class="btn btn-primary">
                        Create First Post
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.community-header-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.community-icon-large {
    font-size: 4rem;
}

.community-title {
    color: white;
    margin-bottom: 10px;
}

.community-description {
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 15px;
}

.post-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.post-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.user-name {
    font-weight: 600;
    color: #333;
}

.post-title a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
}

.post-title a:hover {
    color: #007bff;
    text-decoration: none;
}

.post-excerpt {
    color: #666;
    line-height: 1.6;
    margin: 15px 0;
}

.post-image {
    margin: 15px 0;
    border-radius: 10px;
    overflow: hidden;
}

.post-image img {
    border-radius: 10px;
}

.post-footer {
    border-top: 1px solid #eee;
    padding-top: 15px;
    margin-top: 15px;
}

.post-actions {
    display: flex;
    gap: 20px;
}

.action-btn {
    background: none;
    border: none;
    color: #666;
    font-size: 14px;
    cursor: pointer;
    transition: color 0.3s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 5px;
}

.action-btn:hover {
    color: #007bff;
    text-decoration: none;
}

.like-btn.liked {
    color: #dc3545;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.empty-icon {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 20px;
}

.badge {
    border-radius: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle like button clicks
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            
            fetch(`/community/post/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const heartIcon = this.querySelector('i');
                const likesCount = this.querySelector('.likes-count');
                
                if (data.liked) {
                    heartIcon.classList.add('text-danger');
                } else {
                    heartIcon.classList.remove('text-danger');
                }
                
                likesCount.textContent = data.likes_count;
            });
        });
    });
});
</script>
@endsection