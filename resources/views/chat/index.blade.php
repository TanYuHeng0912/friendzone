@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Your Chats</h2>
            
            @if($chats->isEmpty())
                <div class="text-center empty">
                    <h3>No chats yet...</h3>
                    <p>Start chatting with your matches!</p>
                    <a href="{{ route('matches') }}" class="btn btn-primary">View Matches</a>
                </div>
            @else
                <div class="chat-list">
                    @foreach($chats as $chat)
                        @php
                            $otherUser = $chat->getOtherUser($user->id);
                            $unreadCount = $chat->getUnreadCount($user->id);
                        @endphp
                        
                        <div class="chat-item">
                            <a href="{{ route('chat.show', $chat->id) }}" class="chat-link">
                                <div class="row align-items-center">
                                    <div class="col-2">
                                        <img src="{{ $otherUser->info->getPicture() }}" 
                                             alt="{{ $otherUser->info->name }}"
                                             class="chat-avatar">
                                    </div>
                                    <div class="col-8">
                                        <h5 class="chat-name">
                                            {{ $otherUser->info->name }} {{ $otherUser->info->surname }}
                                        </h5>
                                        @if($chat->latestMessage)
                                            <p class="chat-preview">
                                                {{ Str::limit($chat->latestMessage->message, 50) }}
                                            </p>
                                            <small class="chat-time">
                                                {{ $chat->latestMessage->created_at->diffForHumans() }}
                                            </small>
                                        @else
                                            <p class="chat-preview text-muted">No messages yet</p>
                                        @endif
                                    </div>
                                    <div class="col-2 text-right">
                                        @if($unreadCount > 0)
                                            <span class="badge badge-primary">{{ $unreadCount }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .empty {
        margin-top: 20%;
        color: #666;
    }

    .chat-list {
        margin-top: 20px;
    }

    .chat-item {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        margin-bottom: 15px;
        padding: 15px;
        transition: all 0.3s ease;
    }

    .chat-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .chat-link {
        text-decoration: none;
        color: inherit;
    }

    .chat-link:hover {
        text-decoration: none;
        color: inherit;
    }

    .chat-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    .chat-name {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .chat-preview {
        margin-bottom: 5px;
        color: #666;
        font-size: 14px;
    }

    .chat-time {
        color: #999;
        font-size: 12px;
    }

    .badge {
        font-size: 12px;
    }
</style>
@endsection