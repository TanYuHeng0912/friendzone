@extends('layouts.app')

@section('content')
<div class="chats-container">
    <div class="chats-header">
        <div class="header-content">
            <h1 class="chats-title">
                <i class="fas fa-comments"></i> Your Chats
            </h1>
            <p class="chats-subtitle">{{ $chats->count() }} {{ $chats->count() == 1 ? 'conversation' : 'conversations' }}</p>
        </div>
    </div>
    
    @if($chats->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-comments"></i>
            </div>
            <h2>No Chats Yet</h2>
            <p>Start a conversation with your matches!</p>
            <a href="{{ route('matches') }}" class="btn-start-chat">
                <i class="fas fa-heart"></i>
                <span>View Matches</span>
            </a>
        </div>
    @else
        <div class="chats-list">
            @foreach($chats as $chat)
                @php
                    $otherUser = $chat->getOtherUser($user->id);
                    $unreadCount = $chat->getUnreadCount($user->id);
                    $latestMessage = $chat->latestMessage;
                @endphp
                
                <a href="{{ route('chat.show', $chat->id) }}" class="chat-card {{ $unreadCount > 0 ? 'chat-unread' : '' }}">
                    <div class="chat-card-content">
                        <!-- Avatar Section -->
                        <div class="chat-avatar-wrapper">
                            <img src="{{ $otherUser->info->getPicture() }}" 
                                 alt="{{ $otherUser->info->name }}"
                                 class="chat-avatar">
                            @if($otherUser->isOnline())
                                <span class="online-badge" title="Online">
                                    <i class="fas fa-circle"></i>
                                </span>
                            @endif
                        </div>

                        <!-- Chat Info Section -->
                        <div class="chat-info">
                            <div class="chat-header-row">
                                <h3 class="chat-name">
                                    {{ $otherUser->info->name }} {{ $otherUser->info->surname }}
                                </h3>
                                @if($latestMessage)
                                    <span class="chat-timestamp">
                                        {{ $latestMessage->created_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="chat-preview-row">
                                @if($latestMessage)
                                    <div class="chat-preview-content">
                                        @if($latestMessage->message_type === 'voice')
                                            <span class="message-type-icon">
                                                <i class="fas fa-microphone"></i>
                                            </span>
                                            <span class="chat-preview-text">Voice message</span>
                                        @elseif($latestMessage->message_type === 'image' || $latestMessage->media_type === 'image')
                                            <span class="message-type-icon">
                                                <i class="fas fa-image"></i>
                                            </span>
                                            <span class="chat-preview-text">Photo</span>
                                        @elseif($latestMessage->message_type === 'video' || $latestMessage->media_type === 'video')
                                            <span class="message-type-icon">
                                                <i class="fas fa-video"></i>
                                            </span>
                                            <span class="chat-preview-text">Video</span>
                                        @else
                                            <span class="chat-preview-text">
                                                {{ Str::limit($latestMessage->message, 60) }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="chat-preview-text text-muted">No messages yet</span>
                                @endif
                            </div>
                        </div>

                        <!-- Unread Badge -->
                        @if($unreadCount > 0)
                            <div class="unread-badge-wrapper">
                                <span class="unread-badge">{{ $unreadCount }}</span>
                            </div>
                        @endif

                        <!-- Arrow Icon -->
                        <div class="chat-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

<style>
.chats-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px 20px;
    min-height: calc(100vh - 200px);
}

.chats-header {
    margin-bottom: 30px;
}

.header-content {
    text-align: center;
}

.chats-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 8px;
}

.chats-title i {
    color: #667eea;
    -webkit-text-fill-color: #667eea;
    margin-right: 10px;
}

.chats-subtitle {
    color: #6c757d;
    font-size: 1rem;
}

.empty-state {
    text-align: center;
    padding: 100px 20px;
}

.empty-icon {
    font-size: 5rem;
    color: #dee2e6;
    margin-bottom: 20px;
}

.empty-state h2 {
    color: #495057;
    margin-bottom: 10px;
    font-size: 2rem;
}

.empty-state p {
    color: #6c757d;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

.btn-start-chat {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 14px 28px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-start-chat:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    text-decoration: none;
    color: white;
}

.chats-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.chat-card {
    display: block;
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.chat-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.chat-card:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    text-decoration: none;
    color: inherit;
    border-color: #e9ecef;
}

.chat-card:hover::before {
    transform: scaleY(1);
}

.chat-card.chat-unread {
    background: linear-gradient(to right, #f8f9ff 0%, #ffffff 10%);
    border-color: #667eea;
    font-weight: 500;
}

.chat-card.chat-unread::before {
    transform: scaleY(1);
}

.chat-card-content {
    display: flex;
    align-items: center;
    gap: 16px;
}

.chat-avatar-wrapper {
    position: relative;
    flex-shrink: 0;
}

.chat-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #f0f0f0;
    transition: all 0.3s ease;
}

.chat-card:hover .chat-avatar {
    border-color: #667eea;
    transform: scale(1.05);
}

.online-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 16px;
    height: 16px;
    background: #28a745;
    border: 3px solid white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.online-badge i {
    font-size: 0.5rem;
    color: white;
}

.chat-info {
    flex: 1;
    min-width: 0;
}

.chat-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.chat-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-unread .chat-name {
    color: #667eea;
    font-weight: 700;
}

.chat-timestamp {
    font-size: 0.85rem;
    color: #6c757d;
    white-space: nowrap;
    margin-left: 12px;
}

.chat-preview-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.chat-preview-content {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 0;
}

.message-type-icon {
    color: #667eea;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.chat-preview-text {
    font-size: 0.9rem;
    color: #6c757d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-unread .chat-preview-text {
    color: #495057;
    font-weight: 500;
}

.unread-badge-wrapper {
    flex-shrink: 0;
}

.unread-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.chat-arrow {
    flex-shrink: 0;
    color: #dee2e6;
    transition: all 0.3s ease;
}

.chat-card:hover .chat-arrow {
    color: #667eea;
    transform: translateX(3px);
}

.chat-arrow i {
    font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .chats-container {
        padding: 20px 15px;
    }
    
    .chats-title {
        font-size: 2rem;
    }
    
    .chat-card {
        padding: 16px;
    }
    
    .chat-avatar {
        width: 56px;
        height: 56px;
    }
    
    .chat-name {
        font-size: 1rem;
    }
    
    .chat-preview-text {
        font-size: 0.85rem;
    }
}

@media (max-width: 480px) {
    .chats-title {
        font-size: 1.75rem;
    }
    
    .chat-card-content {
        gap: 12px;
    }
    
    .chat-avatar {
        width: 48px;
        height: 48px;
    }
    
    .chat-header-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .chat-timestamp {
        margin-left: 0;
        font-size: 0.8rem;
    }
}
</style>
@endsection