@extends('layouts.app')

@section('content')
<div class="post-page">
    <div class="post-container">
        <!-- Back Button -->
        <div class="back-button-wrapper">
            <a href="{{ route('community.show', $community) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Back to {{ $community->name }}</span>
            </a>
        </div>

        <!-- Post Content -->
        <article class="post-detail-card">
            <div class="post-header">
                <div class="author-section">
                    <img src="{{ $post->user->info->getPicture() }}" 
                         alt="{{ $post->user->info->name }}"
                         class="author-avatar">
                    <div class="author-info">
                        <h5 class="author-name">{{ $post->user->info->name }} {{ $post->user->info->surname }}</h5>
                        <div class="post-meta">
                            <span class="post-time">
                                <i class="far fa-clock"></i> {{ $post->time_ago }}
                            </span>
                            <span class="post-community">
                                <i class="fas fa-users"></i> {{ $community->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="post-body">
                <h1 class="post-title">{{ $post->title }}</h1>
                
                @if($post->image)
                    <div class="post-media">
                        <img src="{{ $post->getImageUrl() }}" 
                             alt="Post image" 
                             class="post-image"
                             onclick="openImageModal('{{ $post->getImageUrl() }}')">
                    </div>
                @endif
                
                <div class="post-text">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>

            <div class="post-footer">
                <div class="post-actions">
                    <button class="action-button like-button {{ $post->isLikedBy(auth()->id()) ? 'liked' : '' }}" 
                            data-post-id="{{ $post->id }}">
                        <i class="fas fa-heart"></i>
                        <span class="action-label">
                            <span class="likes-count">{{ $post->likes_count }}</span> 
                            <span class="action-text">{{ $post->likes_count == 1 ? 'Like' : 'Likes' }}</span>
                        </span>
                    </button>
                    <div class="action-button comment-button">
                        <i class="fas fa-comment"></i>
                        <span class="action-label">
                            {{ $post->comments_count }} 
                            <span class="action-text">{{ $post->comments_count == 1 ? 'Comment' : 'Comments' }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </article>

        <!-- Comments Section -->
        <div class="comments-section">
            <div class="comments-header">
                <h3 class="comments-title">
                    <i class="fas fa-comments"></i> 
                    <span>Comments</span>
                    <span class="comments-count">({{ $post->comments_count }})</span>
                </h3>
            </div>

            <!-- Add Comment Form -->
            <div class="add-comment-card">
                <form action="{{ route('community.comment', $post) }}" method="POST" class="comment-form">
                    @csrf
                    <div class="comment-form-wrapper">
                        <img src="{{ auth()->user()->info->getPicture() }}" 
                             alt="Your avatar" 
                             class="commenter-avatar">
                        <div class="comment-input-wrapper">
                            <textarea class="comment-input @error('content') is-invalid @enderror" 
                                      name="content" 
                                      rows="3" 
                                      placeholder="Write a comment..."
                                      required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="comment-submit">
                                <button type="submit" class="btn-submit-comment">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Post Comment</span>
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
                            <img src="{{ $comment->user->info->getPicture() }}" 
                                 alt="User avatar" 
                                 class="comment-avatar">
                            <div class="comment-content-wrapper">
                                <div class="comment-content">
                                    <div class="comment-header">
                                        <strong class="comment-author">
                                            {{ $comment->user->info->name }} {{ $comment->user->info->surname }}
                                        </strong>
                                        <span class="comment-time">{{ $comment->time_ago }}</span>
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
                                    <form action="{{ route('community.comment', $post) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <div class="reply-form-wrapper">
                                            <img src="{{ auth()->user()->info->getPicture() }}" 
                                                 alt="Your avatar" 
                                                 class="reply-avatar">
                                            <div class="reply-input-wrapper">
                                                <textarea class="reply-input" 
                                                          name="content" 
                                                          rows="2" 
                                                          placeholder="Write a reply..."
                                                          required></textarea>
                                                <div class="reply-buttons">
                                                    <button type="submit" class="btn-reply-submit">
                                                        Reply
                                                    </button>
                                                    <button type="button" class="btn-reply-cancel" 
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
                                                <img src="{{ $reply->user->info->getPicture() }}" 
                                                     alt="User avatar" 
                                                     class="reply-avatar">
                                                <div class="reply-content">
                                                    <div class="comment-header">
                                                        <strong class="comment-author">
                                                            {{ $reply->user->info->name }} {{ $reply->user->info->surname }}
                                                        </strong>
                                                        <span class="comment-time">{{ $reply->time_ago }}</span>
                                                    </div>
                                                    <div class="comment-text">
                                                        {!! nl2br(e($reply->content)) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-comments">
                    <div class="empty-content">
                        <i class="fas fa-comment-slash empty-icon"></i>
                        <h4>No comments yet</h4>
                        <p>Be the first to share your thoughts!</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="image-modal" id="imageModal" onclick="closeImageModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-image" id="modalImage">
</div>

<style>
.post-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px 20px;
    min-height: calc(100vh - 200px);
}

.post-container {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Back Button */
.back-button-wrapper {
    margin-bottom: 10px;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    padding: 10px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.btn-back:hover {
    background: #f0f2f5;
    text-decoration: none;
    color: #764ba2;
}

/* Post Card */
.post-detail-card {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
}

.post-header {
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e9ecef;
}

.author-section {
    display: flex;
    align-items: center;
    gap: 16px;
}

.author-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e9ecef;
}

.author-info {
    flex: 1;
}

.author-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 6px 0;
}

.post-meta {
    display: flex;
    gap: 16px;
    font-size: 0.9rem;
    color: #6c757d;
}

.post-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.post-body {
    margin-bottom: 24px;
}

.post-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 20px 0;
    line-height: 1.3;
}

.post-media {
    margin: 24px 0;
    border-radius: 12px;
    overflow: hidden;
}

.post-image {
    width: 100%;
    max-height: 600px;
    object-fit: cover;
    border-radius: 12px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.post-image:hover {
    transform: scale(1.02);
}

.post-text {
    color: #495057;
    font-size: 1.05rem;
    line-height: 1.8;
    margin-top: 20px;
}

.post-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 20px;
    margin-top: 20px;
}

.post-actions {
    display: flex;
    gap: 32px;
}

.action-button {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 8px;
}

.action-button:hover {
    background: #f8f9fa;
    color: #667eea;
}

.like-button.liked {
    color: #f02849;
}

.like-button.liked:hover {
    background: #fff5f5;
    color: #f02849;
}

.action-label {
    display: flex;
    align-items: center;
    gap: 4px;
}

.action-text {
    font-size: 0.9rem;
}

/* Comments Section */
.comments-section {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
}

.comments-header {
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e9ecef;
}

.comments-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.comments-count {
    color: #6c757d;
    font-weight: 500;
}

/* Add Comment Form */
.add-comment-card {
    background: #f8f9fa;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 24px;
}

.comment-form-wrapper {
    display: flex;
    gap: 12px;
}

.commenter-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.comment-input-wrapper {
    flex: 1;
}

.comment-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 0.95rem;
    resize: vertical;
    transition: border-color 0.2s ease;
    font-family: inherit;
}

.comment-input:focus {
    outline: none;
    border-color: #667eea;
}

.comment-submit {
    margin-top: 12px;
    display: flex;
    justify-content: flex-end;
}

.btn-submit-comment {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-submit-comment:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Comments List */
.comments-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.comment-item {
    display: flex;
    gap: 12px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.comment-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.comment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.comment-content-wrapper {
    flex: 1;
}

.comment-content {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 14px 16px;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.comment-author {
    color: #2c3e50;
    font-size: 0.95rem;
    font-weight: 600;
}

.comment-time {
    color: #6c757d;
    font-size: 0.85rem;
}

.comment-text {
    color: #495057;
    line-height: 1.6;
    font-size: 0.95rem;
    margin-bottom: 10px;
}

.comment-actions {
    margin-top: 8px;
}

.reply-btn {
    background: none;
    border: none;
    color: #667eea;
    font-size: 0.85rem;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}

.reply-btn:hover {
    background: #e9ecef;
}

/* Reply Form */
.reply-form {
    margin-top: 12px;
    padding: 16px;
    background: white;
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.reply-form-wrapper {
    display: flex;
    gap: 10px;
}

.reply-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.reply-input-wrapper {
    flex: 1;
}

.reply-input {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.9rem;
    resize: vertical;
    font-family: inherit;
}

.reply-input:focus {
    outline: none;
    border-color: #667eea;
}

.reply-buttons {
    margin-top: 10px;
    display: flex;
    gap: 8px;
}

.btn-reply-submit {
    background: #667eea;
    color: white;
    border: none;
    padding: 6px 16px;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-reply-submit:hover {
    background: #764ba2;
}

.btn-reply-cancel {
    background: #e9ecef;
    color: #6c757d;
    border: none;
    padding: 6px 16px;
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-reply-cancel:hover {
    background: #dee2e6;
}

/* Replies */
.replies {
    margin-top: 12px;
    padding-left: 20px;
    border-left: 3px solid #e9ecef;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.reply-item {
    display: flex;
    gap: 10px;
}

.reply-content {
    flex: 1;
    background: #f8f9fa;
    border-radius: 10px;
    padding: 12px 14px;
}

/* Empty State */
.no-comments {
    text-align: center;
    padding: 60px 20px;
}

.empty-content {
    max-width: 400px;
    margin: 0 auto;
}

.empty-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 16px;
}

.no-comments h4 {
    color: #495057;
    margin-bottom: 8px;
}

.no-comments p {
    color: #6c757d;
    font-size: 1rem;
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

/* Responsive */
@media (max-width: 768px) {
    .post-page {
        padding: 20px 15px;
    }
    
    .post-detail-card,
    .comments-section {
        padding: 24px;
    }
    
    .post-title {
        font-size: 1.5rem;
    }
    
    .post-actions {
        gap: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle like button clicks
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const button = this;
            const heartIcon = this.querySelector('i');
            const likesCount = this.querySelector('.likes-count');
            const actionText = this.querySelector('.action-text');
            
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
                    if (actionText) {
                        actionText.textContent = (data.likes_count || 0) == 1 ? 'Like' : 'Likes';
                    }
                }
            })
            .catch(error => {
                console.error('Error liking post:', error);
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
