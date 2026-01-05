@extends('layouts.app')

@section('content')
<div class="community-page">
    <!-- Community Header -->
    <div class="community-banner">
        <div class="banner-content">
            <div class="banner-left">
                <div class="community-icon-wrapper">
                    <div class="community-icon-large">{{ $community->icon }}</div>
                </div>
                <div class="community-info">
                    <h1 class="community-title">{{ $community->name }}</h1>
                    <p class="community-description">{{ $community->description }}</p>
                    <div class="community-stats-bar">
                        <div class="stat-badge">
                            <i class="fas fa-users"></i>
                            <span>{{ $community->members_count }} {{ $community->members_count == 1 ? 'Member' : 'Members' }}</span>
                        </div>
                        <div class="stat-badge">
                            <i class="fas fa-newspaper"></i>
                            <span>{{ $community->posts_count }} {{ $community->posts_count == 1 ? 'Post' : 'Posts' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="banner-right">
                <a href="{{ route('community.create-post', $community) }}" class="btn-create-post">
                    <i class="fas fa-plus"></i>
                    <span>Create Post</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert-container">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Posts Section -->
    <div class="posts-container">
        @if($posts->count() > 0)
            <div class="posts-list">
                @foreach($posts as $post)
                    <article class="post-card">
                        <div class="post-header">
                            <div class="author-info">
                                <img src="{{ $post->user->info->getPicture() }}" 
                                     alt="{{ $post->user->info->name }}"
                                     class="author-avatar">
                                <div class="author-details">
                                    <h5 class="author-name">
                                        {{ $post->user->info->name }} {{ $post->user->info->surname }}
                                    </h5>
                                    <span class="post-time">{{ $post->time_ago }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="post-body">
                            <h2 class="post-title">
                                <a href="{{ route('community.post', [$community, $post]) }}">{{ $post->title }}</a>
                            </h2>
                            
                            <div class="post-text">
                                <p>{{ Str::limit($post->content, 300) }}</p>
                                @if(strlen($post->content) > 300)
                                    <a href="{{ route('community.post', [$community, $post]) }}" class="read-more">Read more</a>
                                @endif
                            </div>
                            
                            @if($post->image)
                                <div class="post-media">
                                    <a href="{{ route('community.post', [$community, $post]) }}">
                                        <img src="{{ $post->getImageUrl() }}" 
                                             alt="Post image" 
                                             class="post-image"
                                             onclick="event.preventDefault(); openImageModal('{{ $post->getImageUrl() }}')">
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="post-footer">
                            <div class="post-actions">
                                <button class="action-button like-button {{ $post->isLikedBy(auth()->id()) ? 'liked' : '' }}" 
                                        data-post-id="{{ $post->id }}">
                                    <i class="fas fa-heart"></i>
                                    <span class="action-count likes-count">{{ $post->likes_count }}</span>
                                </button>
                                <a href="{{ route('community.post', [$community, $post]) }}" class="action-button comment-button">
                                    <i class="fas fa-comment"></i>
                                    <span class="action-count">{{ $post->comments_count }}</span>
                                </a>
                                <a href="{{ route('community.post', [$community, $post]) }}" class="action-button view-button">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($posts->hasPages())
                <div class="pagination-wrapper">
                    {{ $posts->links() }}
                </div>
            @endif
        @else
            <div class="empty-posts">
                <div class="empty-content">
                    <div class="empty-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h2>No Posts Yet</h2>
                    <p>Be the first to share something with this community!</p>
                    <a href="{{ route('community.create-post', $community) }}" class="btn-create-first">
                        <i class="fas fa-plus"></i>
                        <span>Create First Post</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Image Modal -->
<div class="image-modal" id="imageModal" onclick="closeImageModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-image" id="modalImage">
</div>

<style>
.community-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px 20px;
    min-height: calc(100vh - 200px);
}

/* Community Banner */
.community-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.community-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.3;
    }
}

.banner-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 1;
}

.banner-left {
    display: flex;
    align-items: center;
    gap: 24px;
    flex: 1;
}

.community-icon-wrapper {
    flex-shrink: 0;
}

.community-icon-large {
    font-size: 5rem;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.community-info {
    flex: 1;
}

.community-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin: 0 0 12px 0;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.community-description {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.95);
    margin: 0 0 20px 0;
    line-height: 1.6;
}

.community-stats-bar {
    display: flex;
    gap: 16px;
}

.stat-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 8px 16px;
    border-radius: 20px;
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.stat-badge i {
    font-size: 0.9rem;
}

.banner-right {
    flex-shrink: 0;
}

.btn-create-post {
    background: white;
    color: #667eea;
    padding: 14px 28px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.btn-create-post:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    text-decoration: none;
    color: #667eea;
}

/* Alerts */
.alert-container {
    margin-bottom: 30px;
}

.alert {
    padding: 16px 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-close {
    margin-left: auto;
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.alert-close:hover {
    opacity: 1;
}

/* Posts Container */
.posts-container {
    margin-top: 30px;
}

.posts-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Post Card */
.post-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-color: #667eea;
}

.post-header {
    margin-bottom: 16px;
}

.author-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.author-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease;
}

.post-card:hover .author-avatar {
    border-color: #667eea;
}

.author-details {
    flex: 1;
}

.author-name {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 4px 0;
}

.post-time {
    font-size: 0.85rem;
    color: #6c757d;
}

.post-body {
    margin-bottom: 20px;
}

.post-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 12px 0;
    line-height: 1.4;
}

.post-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
}

.post-title a:hover {
    color: #667eea;
    text-decoration: none;
}

.post-text {
    color: #495057;
    font-size: 1rem;
    line-height: 1.7;
    margin-bottom: 16px;
}

.post-text p {
    margin: 0;
}

.read-more {
    color: #667eea;
    font-weight: 600;
    text-decoration: none;
    margin-top: 8px;
    display: inline-block;
}

.read-more:hover {
    text-decoration: underline;
    color: #764ba2;
}

.post-media {
    margin: 16px 0;
    border-radius: 12px;
    overflow: hidden;
}

.post-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: 12px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.post-image:hover {
    transform: scale(1.02);
}

.post-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 16px;
    margin-top: 16px;
}

.post-actions {
    display: flex;
    align-items: center;
    gap: 24px;
}

.action-button {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 8px;
}

.action-button:hover {
    background: #f8f9fa;
    color: #667eea;
    text-decoration: none;
}

.like-button.liked {
    color: #f02849;
}

.like-button.liked:hover {
    background: #fff5f5;
    color: #f02849;
}

.action-count {
    font-weight: 600;
}

.comment-button:hover {
    color: #0084ff;
}

.view-button {
    margin-left: auto;
    color: #667eea;
    font-weight: 600;
}

/* Empty State */
.empty-posts {
    background: white;
    border-radius: 20px;
    padding: 80px 40px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.empty-content {
    max-width: 500px;
    margin: 0 auto;
}

.empty-icon {
    font-size: 5rem;
    color: #dee2e6;
    margin-bottom: 24px;
}

.empty-posts h2 {
    font-size: 2rem;
    color: #2c3e50;
    margin-bottom: 12px;
}

.empty-posts p {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 30px;
}

.btn-create-first {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 14px 32px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-create-first:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    text-decoration: none;
    color: white;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 40px;
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination {
    display: flex;
    gap: 8px;
}

.pagination-wrapper .page-link {
    padding: 10px 16px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    color: #667eea;
    transition: all 0.2s ease;
}

.pagination-wrapper .page-link:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.pagination-wrapper .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
}

/* Image Modal */
.image-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    cursor: pointer;
}

.image-modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-image {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    border-radius: 8px;
}

.modal-close {
    position: absolute;
    top: 30px;
    right: 50px;
    color: white;
    font-size: 50px;
    font-weight: bold;
    cursor: pointer;
    z-index: 2001;
    transition: opacity 0.2s;
}

.modal-close:hover {
    opacity: 0.7;
}

/* Responsive Design */
@media (max-width: 768px) {
    .community-banner {
        padding: 30px 20px;
    }
    
    .banner-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }
    
    .banner-left {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }
    
    .community-icon-large {
        font-size: 4rem;
    }
    
    .community-title {
        font-size: 2rem;
    }
    
    .btn-create-post {
        width: 100%;
        justify-content: center;
    }
    
    .post-card {
        padding: 20px;
    }
    
    .post-title {
        font-size: 1.3rem;
    }
}

@media (max-width: 480px) {
    .community-page {
        padding: 20px 15px;
    }
    
    .community-banner {
        padding: 24px 16px;
    }
    
    .community-title {
        font-size: 1.75rem;
    }
    
    .community-stats-bar {
        flex-direction: column;
        gap: 8px;
        width: 100%;
    }
    
    .stat-badge {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// Like functionality
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const postId = this.dataset.postId;
            const button = this;
            const heartIcon = this.querySelector('i');
            const likesCount = this.querySelector('.likes-count');
            
            fetch(`/community/post/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.liked) {
                    button.classList.add('liked');
                    heartIcon.classList.add('fas');
                    heartIcon.classList.remove('far');
                } else {
                    button.classList.remove('liked');
                    heartIcon.classList.remove('fas');
                    heartIcon.classList.add('far');
                }
                
                if (likesCount) {
                    likesCount.textContent = data.likes_count || 0;
                }
            })
            .catch(error => {
                console.error('Error liking post:', error);
            });
        });
    });
});

// Image Modal
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const img = document.getElementById('modalImage');
    img.src = imageSrc;
    modal.classList.add('show');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.remove('show');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection

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
