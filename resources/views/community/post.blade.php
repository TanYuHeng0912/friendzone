@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('community.show', $community) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to {{ $community->name }}
                </a>
            </div>

            <!-- Post Content -->
            <div class="post-detail-card">
                <div class="post-header">
                    <div class="d-flex align-items-center">
                        <img src="{{ $post->user->info->getPicture() }}" 
                             alt="User avatar" class="user-avatar">
                        <div class="ml-3">
                            <h6 class="user-name mb-0">{{ $post->user->info->name }} {{ $post->user->info->surname }}</h6>
                            <small class="text-muted">{{ $post->time_ago }} in {{ $community->name }}</small>
                        </div>
                    </div>
                </div>

                <div class="post-content">
                    <h2 class="post-title">{{ $post->title }}</h2>
                    
                    @if($post->image)
                        <div class="post-image mb-3">
                            <img src="{{ $post->getImageUrl() }}" alt="Post image" class="img-fluid">
                        </div>
                    @endif
                    
                    <div class="post-text">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                </div>

                <div class="post-footer">
                    <div class="post-actions">
                        <button class="action-btn like-btn" data-post-id="{{ $post->id }}">
                            <i class="fas fa-heart {{ $post->isLikedBy(auth()->id()) ? 'text-danger' : '' }}"></i>
                            <span class="likes-count">{{ $post->likes_count }}</span> likes
                        </button>
                        <span class="action-btn">
                            <i class="fas fa-comment"></i>
                            {{ $post->comments_count }} comments
                        </span>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="comments-section">
                <h4 class="comments-title">
                    <i class="fas fa-comments"></i> Comments ({{ $post->comments_count }})
                </h4>

                <!-- Add Comment Form -->
                <div class="add-comment-card">
                    <form action="{{ route('community.comment', $post) }}" method="POST">
                        @csrf
                        <div class="d-flex">
                            <img src="{{ auth()->user()->info->getPicture() }}" 
                                 alt="Your avatar" class="comment-avatar">
                            <div class="flex-grow-1 ml-3">
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          name="content" 
                                          rows="3" 
                                          placeholder="Write a comment..."
                                          required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-paper-plane"></i> Post Comment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Comments List -->
                @if($post->comments->count() > 0)
                    <div class="comments-list">
                        @foreach($post->comments as $comment)
                            <div class="comment-item">
                                <div class="d-flex">
                                    <img src="{{ $comment->user->info->getPicture() }}" 
                                         alt="User avatar" class="comment-avatar">
                                    <div class="flex-grow-1 ml-3">
                                        <div class="comment-content">
                                            <div class="comment-header">
                                                <strong class="comment-author">
                                                    {{ $comment->user->info->name }} {{ $comment->user->info->surname }}
                                                </strong>
                                                <small class="comment-time text-muted ml-2">
                                                    {{ $comment->time_ago }}
                                                </small>
                                            </div>
                                            <div class="comment-text">
                                                {!! nl2br(e($comment->content)) !!}
                                            </div>
                                            <div class="comment-actions">
                                                <button class="reply-btn" data-comment-id="{{ $comment->id }}">
                                                    <i class="fas fa-reply"></i> Reply
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Reply Form (Initially Hidden) -->
                                        <div class="reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                                            <form action="{{ route('community.comment', $post) }}" method="POST" class="mt-2">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <div class="d-flex">
                                                    <img src="{{ auth()->user()->info->getPicture() }}" 
                                                         alt="Your avatar" class="comment-avatar-small">
                                                    <div class="flex-grow-1 ml-2">
                                                        <textarea class="form-control" 
                                                                  name="content" 
                                                                  rows="2" 
                                                                  placeholder="Write a reply..."
                                                                  required></textarea>
                                                        <div class="mt-2">
                                                            <button type="submit" class="btn btn-primary btn-sm">
                                                                Reply
                                                            </button>
                                                            <button type="button" class="btn btn-secondary btn-sm ml-1" 
                                                                    onclick="hideReplyForm({{ $comment->id }})">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Replies -->
                                        @if($comment->replies->count() > 0)
                                            <div class="replies">
                                                @foreach($comment->replies as $reply)
                                                    <div class="reply-item">
                                                        <div class="d-flex">
                                                            <img src="{{ $reply->user->info->getPicture() }}" 
                                                                 alt="User avatar" class="comment-avatar-small">
                                                            <div class="flex-grow-1 ml-2">
                                                                <div class="comment-content">
                                                                    <div class="comment-header">
                                                                        <strong class="comment-author">
                                                                            {{ $reply->user->info->name }} {{ $reply->user->info->surname }}
                                                                        </strong>
                                                                        <small class="comment-time text-muted ml-2">
                                                                            {{ $reply->time_ago }}
                                                                        </small>
                                                                    </div>
                                                                    <div class="comment-text">
                                                                        {!! nl2br(e($reply->content)) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-comments">
                        <div class="text-center py-4">
                            <i class="fas fa-comment-slash empty-icon"></i>
                            <p class="text-muted">No comments yet. Be the first to share your thoughts!</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.post-detail-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.comment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.comment-avatar-small {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}

.user-name {
    font-weight: 600;
    color: #333;
}

.post-title {
    color: #333;
    font-weight: 700;
    margin: 20px 0;
    line-height: 1.3;
}

.post-image {
    border-radius: 10px;
    overflow: hidden;
}

.post-image img {
    border-radius: 10px;
}

.post-text {
    color: #444;
    line-height: 1.7;
    font-size: 16px;
    margin-bottom: 20px;
}

.post-footer {
    border-top: 1px solid #eee;
    padding-top: 20px;
    margin-top: 20px;
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
    display: flex;
    align-items: center;
    gap: 5px;
}

.action-btn:hover {
    color: #007bff;
}

.comments-section {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.comments-title {
    color: #333;
    margin-bottom: 25px;
    font-weight: 600;
}

.add-comment-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
}

.comment-item {
    border-bottom: 1px solid #f0f0f0;
    padding: 20px 0;
}

.comment-item:last-child {
    border-bottom: none;
}

.comment-content {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
}

.comment-header {
    margin-bottom: 8px;
}

.comment-author {
    color: #333;
    font-size: 14px;
}

.comment-time {
    font-size: 12px;
}

.comment-text {
    color: #555;
    line-height: 1.5;
    margin-bottom: 10px;
}

.comment-actions {
    margin-top: 10px;
}

.reply-btn {
    background: none;
    border: none;
    color: #666;
    font-size: 12px;
    cursor: pointer;
    transition: color 0.3s ease;
}

.reply-btn:hover {
    color: #007bff;
}

.replies {
    margin-top: 15px;
    padding-left: 20px;
    border-left: 2px solid #e9ecef;
}

.reply-item {
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.reply-item:last-child {
    border-bottom: none;
}

.reply-form {
    margin-top: 15px;
    padding: 15px;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.no-comments {
    text-align: center;
    padding: 40px 0;
}

.empty-icon {
    font-size: 3rem;
    color: #ccc;
    margin-bottom: 15px;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    border-radius: 6px;
    font-weight: 500;
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
}

.btn-secondary {
    background: #6c757d;
    border: none;
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

    // Handle reply button clicks
    document.querySelectorAll('.reply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            
            if (replyForm.style.display === 'none') {
                // Hide all other reply forms
                document.querySelectorAll('.reply-form').forEach(form => {
                    form.style.display = 'none';
                });
                
                // Show this reply form
                replyForm.style.display = 'block';
                replyForm.querySelector('textarea').focus();
            } else {
                replyForm.style.display = 'none';
            }
        });
    });
});

function hideReplyForm(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    replyForm.style.display = 'none';
}
</script>
@endsection