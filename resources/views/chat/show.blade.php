@extends('layouts.app')

@section('content')
<div class="messenger-container">
    <!-- Main Chat Area -->
    <div class="messenger-main">
        <!-- Chat Header -->
        <div class="messenger-header">
            <div class="header-left">
                <a href="{{ route('chat.index') }}" class="back-icon">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="user-avatar-wrapper">
                    <img src="{{ $otherUser->info->getPicture() }}" 
                         alt="{{ $otherUser->info->name }}"
                         class="user-avatar">
                    @if($otherUser->isOnline())
                        <span class="status-indicator online"></span>
                    @else
                        <span class="status-indicator offline"></span>
                    @endif
                </div>
                <div class="user-details">
                    <h3 class="user-name">{{ $otherUser->info->name }} {{ $otherUser->info->surname }}</h3>
                    <span class="user-status">
                        @if($otherUser->isOnline())
                            Active now
                        @else
                            {{ $otherUser->last_seen_at ? 'Active ' . $otherUser->last_seen_at->diffForHumans() : 'Offline' }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="header-right">
                <button class="header-icon-btn" onclick="startVoiceCall()" title="Voice call">
                    <i class="fas fa-phone"></i>
                </button>
                <button class="header-icon-btn" onclick="startVideoCall()" title="Video call">
                    <i class="fas fa-video"></i>
                </button>
                <button class="header-icon-btn" onclick="toggleSidebar()" title="Chat info">
                    <i class="fas fa-info-circle"></i>
                </button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="messenger-messages" id="chatMessages">
            @php
                // Combine messages and calls, sorted by created_at
                $allItems = collect();
                
                // Add messages
                foreach($chat->messages as $message) {
                    $allItems->push([
                        'type' => 'message',
                        'item' => $message,
                        'created_at' => $message->created_at
                    ]);
                }
                
                // Add calls
                if(isset($calls)) {
                    foreach($calls as $call) {
                        $allItems->push([
                            'type' => 'call',
                            'item' => $call,
                            'created_at' => $call->created_at
                        ]);
                    }
                }
                
                // Sort by created_at
                $allItems = $allItems->sortBy('created_at');
            @endphp
            
            @foreach($allItems as $item)
                @if($item['type'] === 'call')
                    @php $call = $item['item']; @endphp
                    <div class="call-record-wrapper">
                        <div class="call-record {{ $call->status === 'missed' ? 'missed' : '' }} {{ $call->status === 'rejected' ? 'rejected' : '' }}">
                            <div class="call-icon">
                                @if($call->status === 'answered')
                                    <i class="fas fa-phone"></i>
                                @elseif($call->status === 'missed' || $call->status === 'rejected')
                                    <i class="fas fa-phone-slash"></i>
                                @else
                                    <i class="fas fa-phone"></i>
                                @endif
                            </div>
                            <div class="call-info">
                                <div class="call-status">
                                    @if($call->caller_id == $user->id)
                                        @if($call->status === 'answered')
                                            Outgoing call
                                        @elseif($call->status === 'missed')
                                            Missed call
                                        @elseif($call->status === 'rejected')
                                            Call declined
                                        @else
                                            Outgoing call
                                        @endif
                                    @else
                                        @if($call->status === 'answered')
                                            Incoming call
                                        @elseif($call->status === 'missed')
                                            Missed call
                                        @elseif($call->status === 'rejected')
                                            Call declined
                                        @else
                                            Incoming call
                                        @endif
                                    @endif
                                </div>
                                <div class="call-time">
                                    @if($call->status === 'answered' && $call->duration)
                                        {{ gmdate('i:s', $call->duration) }}
                                    @else
                                        {{ $call->created_at->format('H:i') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @php $message = $item['item']; @endphp
                <div class="message-wrapper {{ $message->sender_id == $user->id ? 'message-sent' : 'message-received' }}"
                     data-message-id="{{ $message->id }}">
                    @if($message->message_type === 'voice')
                        <div class="message-bubble voice-message">
                            <div class="voice-player">
                                <button class="voice-play-btn" onclick="toggleAudio(this, '{{ asset('storage/' . $message->message) }}')">
                                    <i class="fas fa-play"></i>
                                </button>
                                <div class="voice-waveform">
                                    <div class="voice-duration">Voice message</div>
                                </div>
                            </div>
                            <div class="message-time">{{ $message->formatted_time }}</div>
                        </div>
                    @else
                        <div class="message-bubble">
                            @if($message->media_type)
                                <div class="message-media">
                                    @if($message->media_type === 'image')
                                        <img src="{{ asset('storage/' . $message->media_path) }}" 
                                             alt="Image" 
                                             class="message-image"
                                             onclick="openMediaModal('{{ asset('storage/' . $message->media_path) }}', 'image')">
                                    @elseif($message->media_type === 'video')
                                        <video controls class="message-video">
                                            <source src="{{ asset('storage/' . $message->media_path) }}" type="video/mp4">
                                        </video>
                                    @endif
                                </div>
                            @endif
                            
                            @if($message->message)
                                <div class="message-text">{{ $message->message }}</div>
                            @endif
                            
                            <div class="message-footer">
                                <span class="message-time">{{ $message->formatted_time }}</span>
                                @if($message->sender_id == $user->id)
                                    <i class="fas fa-check{{ $message->is_read ? '-double' : '' }} read-status"></i>
                                @endif
                            </div>
                            
                            @if($message->reactions->count() > 0)
                                <div class="message-reactions">
                                    @foreach($message->reactions->groupBy('reaction') as $reaction => $reactions)
                                        <span class="reaction-item">{{ $reaction }} {{ $reactions->count() }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                @endif
            @endforeach
            
            <!-- Typing Indicator -->
            <div class="typing-indicator" id="typingIndicator" style="display: none;">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="messenger-input-area">
            <div class="reply-preview" id="replyPreview" style="display: none;">
                <div class="reply-content">
                    <i class="fas fa-reply"></i>
                    <div class="reply-info">
                        <strong id="replyPreviewSender"></strong>
                        <span id="replyPreviewText"></span>
                    </div>
                    <button class="reply-close" onclick="cancelReply()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
                <form id="messageForm" onsubmit="event.preventDefault(); sendMessage();">
                <input type="hidden" id="replyToId" name="reply_to_id">
                <div class="input-container">
                    <button type="button" class="input-icon-btn" id="emojiBtn" title="Emoji">
                        <i class="far fa-smile"></i>
                    </button>
                    <label for="mediaUpload" class="input-icon-btn media-upload-label" title="Attach file">
                        <i class="fas fa-plus"></i>
                        <input type="file" id="mediaUpload" accept="image/*,video/*" style="display: none; position: absolute; width: 0; height: 0; opacity: 0;" onchange="handleMediaUpload(event)">
                    </label>
                    <div class="input-wrapper">
                        <input type="text" 
                               id="messageInput" 
                               class="message-input" 
                               placeholder="Aa" 
                               maxlength="1000"
                               autocomplete="off">
                    </div>
                    <button type="button" 
                            id="voiceMessageButton" 
                            class="input-icon-btn voice-btn"
                            title="Record voice">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <button type="submit" class="input-icon-btn send-btn" id="sendBtn" title="Send">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar (Media Gallery & Search) -->
    <div class="messenger-sidebar" id="messengerSidebar">
        <div class="sidebar-header">
            <h3>Chat Info</h3>
            <button class="sidebar-close" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="sidebar-tabs">
            <button class="tab-btn active" onclick="switchTab('media')">
                <i class="fas fa-images"></i> Media
            </button>
            <button class="tab-btn" onclick="switchTab('search')">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
        
        <div class="sidebar-content">
            <!-- Media Tab -->
            <div class="tab-panel active" id="mediaTab">
                <div class="media-grid" id="mediaGrid">
                    @foreach($chat->messages->where('media_type', '!=', null) as $mediaMessage)
                        <div class="media-item" onclick="openMediaModal('{{ asset('storage/' . $mediaMessage->media_path) }}', '{{ $mediaMessage->media_type }}')">
                            @if($mediaMessage->media_type === 'image')
                                <img src="{{ asset('storage/' . $mediaMessage->media_path) }}" alt="Media">
                            @elseif($mediaMessage->media_type === 'video')
                                <div class="video-thumbnail">
                                    <i class="fas fa-play"></i>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Search Tab -->
            <div class="tab-panel" id="searchTab">
                <div class="search-container">
                    <input type="text" 
                           id="messageSearchInput" 
                           class="search-input" 
                           placeholder="Search messages...">
                    <div class="search-results" id="searchResults"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
</div>

<!-- Emoji Picker -->
<div class="emoji-picker" id="emojiPicker">
    <div class="emoji-picker-header">
        <span>Emoji</span>
        <button class="emoji-close" onclick="toggleEmojiPicker()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="emoji-grid">
        @php
            $emojis = ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳', '😏', '😒', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠', '😡', '🤬', '🤯', '😳', '🥵', '🥶', '😱', '😨', '😰', '😥', '😓', '🤗', '🤔', '🤭', '🤫', '🤥', '😶', '😐', '😑', '😬', '🙄', '😯', '😦', '😧', '😮', '😲', '🥱', '😴', '🤤', '😪', '😵', '🤐', '🥴', '🤢', '🤮', '🤧', '😷', '🤒', '🤕', '🤑', '🤠', '😈', '👿', '👹', '👺', '🤡', '💩', '👻', '💀', '☠️', '👽', '👾', '🤖', '🎃', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾'];
        @endphp
        @foreach($emojis as $emoji)
            <span class="emoji-item" onclick="insertEmoji('{{ $emoji }}')">{{ $emoji }}</span>
        @endforeach
    </div>
</div>

<!-- Media Modal -->
<div class="media-modal" id="mediaModal" onclick="closeMediaModal()">
    <span class="media-close">&times;</span>
    <img class="media-modal-content" id="mediaModalImg">
    <video class="media-modal-content" id="mediaModalVideo" controls></video>
</div>

<style>
* {
    box-sizing: border-box;
}

.messenger-container {
    display: flex;
    height: calc(100vh - 60px);
    background: #f0f2f5;
    position: relative;
}

.messenger-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: white;
    position: relative;
}

/* Header */
.messenger-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    background: #ffffff;
    border-bottom: 1px solid #e4e6eb;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    z-index: 10;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.back-icon {
    color: #65676b;
    font-size: 1.2rem;
    padding: 8px;
    border-radius: 50%;
    transition: background 0.2s;
}

.back-icon:hover {
    background: #f0f2f5;
    text-decoration: none;
    color: #65676b;
}

.user-avatar-wrapper {
    position: relative;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.status-indicator {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 12px;
    height: 12px;
    border: 2px solid white;
    border-radius: 50%;
}

.status-indicator.online {
    background: #31a24c;
}

.status-indicator.offline {
    background: #bcc0c4;
}

.user-details {
    flex: 1;
}

.user-name {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #050505;
    margin: 0;
    line-height: 1.3333;
}

.user-status {
    font-size: 0.8125rem;
    color: #65676b;
}

.header-right {
    display: flex;
    gap: 4px;
}

.header-icon-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: transparent;
    color: #65676b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.header-icon-btn:hover {
    background: #f0f2f5;
}

/* Messages */
.messenger-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f0f2f5;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.message-wrapper {
    display: flex;
    width: 100%;
}

.message-sent {
    justify-content: flex-end;
}

.message-received {
    justify-content: flex-start;
}

.message-bubble {
    max-width: 65%;
    padding: 8px 12px;
    border-radius: 18px;
    position: relative;
    word-wrap: break-word;
}

.message-sent .message-bubble {
    background: #0084ff;
    color: white;
    border-bottom-right-radius: 4px;
}

.message-received .message-bubble {
    background: #e4e6eb;
    color: #050505;
    border-bottom-left-radius: 4px;
}

.message-text {
    font-size: 0.9375rem;
    line-height: 1.3333;
    margin: 0;
}

.message-media {
    margin: -8px -12px 8px -12px;
    border-radius: 18px 18px 0 0;
    overflow: hidden;
}

.message-image {
    max-width: 300px;
    max-height: 300px;
    width: 100%;
    height: auto;
    display: block;
    cursor: pointer;
}

.message-video {
    max-width: 300px;
    max-height: 300px;
    width: 100%;
    display: block;
}

.message-footer {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
    justify-content: flex-end;
}

.message-time {
    font-size: 0.6875rem;
    opacity: 0.7;
}

.read-status {
    font-size: 0.75rem;
    opacity: 0.7;
}

.message-reactions {
    margin-top: 4px;
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
}

.reaction-item {
    background: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 0.75rem;
    border: 1px solid #e4e6eb;
}

.voice-message {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
}

.voice-play-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: rgba(0,0,0,0.1);
    color: inherit;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.voice-waveform {
    flex: 1;
}

.voice-duration {
    font-size: 0.875rem;
}

.typing-indicator {
    display: flex;
    align-items: center;
    padding: 8px 12px;
}

.typing-dots {
    display: flex;
    gap: 4px;
}

.typing-dots span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #65676b;
    animation: typing 1.4s infinite;
}

.typing-dots span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

/* Input Area */
.messenger-input-area {
    background: white;
    border-top: 1px solid #e4e6eb;
    padding: 8px;
}

.reply-preview {
    padding: 8px 12px;
    background: #f0f2f5;
    border-bottom: 1px solid #e4e6eb;
}

.reply-content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.reply-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    font-size: 0.8125rem;
}

.reply-close {
    background: none;
    border: none;
    color: #65676b;
    cursor: pointer;
    padding: 4px;
}

.input-container {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    min-height: 52px;
}

.input-container .input-icon-btn,
.input-container .media-upload-label {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    min-width: 36px;
    min-height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    cursor: pointer;
}

.media-upload-label {
    position: relative;
}

.media-upload-label input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.input-icon-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: transparent;
    color: #0084ff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    padding: 0;
}

.input-icon-btn:hover {
    background: #f0f2f5;
}

.input-wrapper {
    flex: 1;
    background: #f0f2f5;
    border-radius: 20px;
    padding: 8px 12px;
}

.message-input {
    width: 100%;
    border: none;
    background: transparent;
    font-size: 0.9375rem;
    color: #050505;
    outline: none;
}

.message-input::placeholder {
    color: #8a8d91;
}

.send-btn {
    color: #0084ff;
}

.voice-btn.recording {
    color: #f02849;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Sidebar */
.messenger-sidebar {
    width: 360px;
    background: white;
    border-left: 1px solid #e4e6eb;
    display: flex;
    flex-direction: column;
    position: fixed;
    right: -360px;
    top: 60px;
    height: calc(100vh - 60px);
    transition: right 0.3s ease;
    z-index: 1000;
    box-shadow: -2px 0 8px rgba(0,0,0,0.1);
}

.messenger-sidebar.open {
    right: 0;
}

.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    border-bottom: 1px solid #e4e6eb;
}

.sidebar-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #050505;
}

.sidebar-close {
    background: none;
    border: none;
    color: #65676b;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
}

.sidebar-close:hover {
    background: #f0f2f5;
}

.sidebar-tabs {
    display: flex;
    border-bottom: 1px solid #e4e6eb;
}

.tab-btn {
    flex: 1;
    padding: 12px;
    border: none;
    background: transparent;
    color: #65676b;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
}

.tab-btn.active {
    color: #0084ff;
    border-bottom-color: #0084ff;
}

.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}

.tab-panel {
    display: none;
}

.tab-panel.active {
    display: block;
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
}

.media-item {
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: 8px;
    cursor: pointer;
}

.media-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-thumbnail {
    width: 100%;
    height: 100%;
    background: #f0f2f5;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #65676b;
}

.search-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.search-input {
    padding: 12px;
    border: 1px solid #e4e6eb;
    border-radius: 20px;
    font-size: 0.9375rem;
    outline: none;
}

.search-results {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.search-result-item {
    padding: 12px;
    background: #f0f2f5;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
}

.search-result-item:hover {
    background: #e4e6eb;
}

.search-result-sender {
    font-size: 0.8125rem;
    font-weight: 600;
    color: #0084ff;
    margin-bottom: 4px;
}

.search-result-text {
    font-size: 0.9375rem;
    color: #050505;
    margin-bottom: 4px;
}

.search-result-time {
    font-size: 0.6875rem;
    color: #65676b;
}

.sidebar-overlay {
    display: none;
    position: fixed;
    top: 60px;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 999;
}

.sidebar-overlay.show {
    display: block;
}

/* Emoji Picker */
.emoji-picker {
    position: absolute;
    bottom: 60px;
    left: 8px;
    width: 320px;
    height: 300px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    display: none;
    flex-direction: column;
    z-index: 1000;
}

.emoji-picker.show {
    display: flex;
}

.emoji-picker-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    border-bottom: 1px solid #e4e6eb;
}

.emoji-close {
    background: none;
    border: none;
    color: #65676b;
    cursor: pointer;
}

.emoji-grid {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 8px;
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 4px;
    max-width: 100%;
}

.emoji-item {
    font-size: 1.5rem;
    cursor: pointer;
    padding: 4px;
    text-align: center;
    border-radius: 4px;
    transition: background 0.2s;
}

.emoji-item:hover {
    background: #f0f2f5;
}

/* Media Modal */
.media-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    cursor: pointer;
}

.media-modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.media-modal-content {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
}

.media-close {
    position: absolute;
    top: 20px;
    right: 40px;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

/* Scrollbar */
.messenger-messages::-webkit-scrollbar,
.sidebar-content::-webkit-scrollbar,
.emoji-grid::-webkit-scrollbar {
    width: 8px;
}

.messenger-messages::-webkit-scrollbar-track,
.sidebar-content::-webkit-scrollbar-track,
.emoji-grid::-webkit-scrollbar-track {
    background: transparent;
}

.messenger-messages::-webkit-scrollbar-thumb,
.sidebar-content::-webkit-scrollbar-thumb,
.emoji-grid::-webkit-scrollbar-thumb {
    background: #bcc0c4;
    border-radius: 4px;
}

.messenger-messages::-webkit-scrollbar-thumb:hover,
.sidebar-content::-webkit-scrollbar-thumb:hover,
.emoji-grid::-webkit-scrollbar-thumb:hover {
    background: #8a8d91;
}

@media (max-width: 768px) {
    .messenger-sidebar {
        width: 100%;
        right: -100%;
    }
}
</style>

<script>
const chatId = {{ $chat->id }};
let lastMessageId = {{ $chat->messages->last()->id ?? 0 }};
let mediaRecorder = null;
let audioChunks = [];
let isRecordingVoice = false;
let pollingInterval = null;
let typingTimeout = null;

// Initialize Laravel Echo for WebSocket support
// Initialize Laravel Echo and WebSocket connection
let websocketRetryCount = 0;
const MAX_WEBSOCKET_RETRIES = 10;

function initializeWebSocket() {
    websocketRetryCount++;
    
    // Try to get config from global variable first (more reliable)
    let config = window.PUSHER_CONFIG;
    let broadcastDriver, pusherKey, pusherCluster, pusherHost, pusherPort, pusherScheme;
    
    if (config) {
        broadcastDriver = config.driver;
        pusherKey = config.key;
        pusherCluster = config.cluster;
        pusherHost = config.host;
        pusherPort = config.port;
        pusherScheme = config.scheme;
    } else {
        // Fallback to meta tags
        broadcastDriver = document.querySelector('meta[name="broadcast-driver"]')?.content;
        pusherKey = document.querySelector('meta[name="pusher-key"]')?.content;
        pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.content || 'mt1';
        pusherHost = document.querySelector('meta[name="pusher-host"]')?.content || '127.0.0.1';
        pusherPort = document.querySelector('meta[name="pusher-port"]')?.content || '6001';
        pusherScheme = document.querySelector('meta[name="pusher-scheme"]')?.content || 'http';
    }
    
    // Debug: Log what we found
    if (websocketRetryCount === 1) {
        console.log('WebSocket Debug - First attempt:', {
            PUSHER_CONFIG: typeof window.PUSHER_CONFIG !== 'undefined' ? 'exists' : 'missing',
            configValue: window.PUSHER_CONFIG,
            metaTags: {
                broadcastDriver: document.querySelector('meta[name="broadcast-driver"]')?.content,
                pusherKey: document.querySelector('meta[name="pusher-key"]')?.content
            }
        });
    }
    
    // If still not found, retry with limit
    if (!broadcastDriver || !pusherKey) {
        if (websocketRetryCount < MAX_WEBSOCKET_RETRIES) {
            console.log(`WebSocket: Config not found, retrying... (${websocketRetryCount}/${MAX_WEBSOCKET_RETRIES})`);
            setTimeout(initializeWebSocket, 200);
            return;
        } else {
            console.error('WebSocket: Config not found after maximum retries. Falling back to AJAX polling.');
            console.error('Please check that PUSHER_APP_KEY is set in .env and broadcasting.default is "pusher"');
            startPolling();
            return;
        }
    }
    
    // Debug: Log all meta tags
    const allMetaTags = Array.from(document.querySelectorAll('meta')).map(m => ({
        name: m.name,
        content: m.content
    }));
    console.log('WebSocket Debug - All meta tags:', allMetaTags);
    console.log('WebSocket Debug:', {
        broadcastDriver: broadcastDriver,
        pusherKey: pusherKey ? 'set (' + pusherKey.substring(0, 10) + '...)' : 'not set',
        pusherAvailable: typeof window.Pusher !== 'undefined',
        echoClassAvailable: typeof window.EchoClass !== 'undefined',
        echoAvailable: typeof window.Echo !== 'undefined'
    });
    
    if (broadcastDriver === 'pusher' && pusherKey) {
        // Import Echo and Pusher dynamically
        if (typeof window.Pusher === 'undefined') {
            console.log('WebSocket: Pusher JS not loaded, using AJAX polling');
            startPolling();
            return;
        }
        
        // Initialize Echo - wait a bit for app.js to load
        setTimeout(function() {
            if (typeof window.Echo === 'undefined' && typeof window.EchoClass !== 'undefined') {
                try {
                    window.Echo = new window.EchoClass({
                        broadcaster: 'pusher',
                        key: pusherKey,
                        cluster: pusherCluster,
                        wsHost: pusherHost,
                        wsPort: pusherPort,
                        wssPort: pusherPort,
                        forceTLS: pusherScheme === 'https',
                        encrypted: pusherScheme === 'https',
                        disableStats: true,
                        enabledTransports: ['ws', 'wss'],
                    });
                    console.log('WebSocket: Laravel Echo initialized');
                } catch (error) {
                    console.error('WebSocket: Echo initialization failed', error);
                    startPolling();
                    return;
                }
            } else if (typeof window.Echo === 'undefined') {
                console.log('WebSocket: Echo class not available, using AJAX polling');
                startPolling();
                return;
            }
            
            // Subscribe to chat channel
            try {
                console.log('WebSocket: Connecting to chat channel...');
                const channel = window.Echo.private(`chat.${chatId}`);
                
                // Listen for new messages
                channel.listen('.message.sent', (data) => {
                    console.log('WebSocket: New message received', data);
                    // The broadcastWith() method returns data directly at the top level
                    // So data.id, data.message, etc. are already at the top level
                    if (data && data.id && data.id > lastMessageId) {
                        // Don't add message if it's from the current user (already added from AJAX response)
                        // toOthers() should prevent this, but we add this check as a safety measure
                        const currentUserId = {{ $user->id }};
                        if (data.sender_id === currentUserId) {
                            console.log('WebSocket: Ignoring own message (already added from AJAX response)', data.id);
                            lastMessageId = data.id; // Update lastMessageId to prevent duplicates
                            return;
                        }
                        
                        // The data structure already matches what addMessage expects
                        addMessage(data);
                        lastMessageId = data.id;
                        scrollToBottom();
                    } else {
                        console.log('WebSocket: Message ignored', {
                            hasData: !!data,
                            hasId: !!(data && data.id),
                            messageId: data ? data.id : null,
                            lastMessageId: lastMessageId,
                            shouldAdd: data && data.id && data.id > lastMessageId
                        });
                    }
                });
                
                // Listen for typing indicators
                channel.listen('.user.typing', (data) => {
                    console.log('WebSocket: User typing', data);
                    showTypingIndicator(data.user_id, data.user_name);
                });
                
                console.log('WebSocket: Connected to chat channel');
            } catch (error) {
                console.error('WebSocket: Channel subscription failed', error);
                startPolling();
            }
        }, 500);
    } else {
        console.log('WebSocket: Not configured, using AJAX polling');
        startPolling();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for app.js to load EchoClass
    setTimeout(initializeWebSocket, 200);
});

// AJAX Polling fallback
function startPolling() {
    if (pollingInterval) return;
    
    pollingInterval = setInterval(function() {
        fetch(`/chat/${chatId}/messages?after=${lastMessageId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (msg.id > lastMessageId) {
                        addMessage(msg);
                        lastMessageId = msg.id;
                    }
                });
                scrollToBottom();
            }
        })
        .catch(error => console.error('Polling error:', error));
    }, 3000); // Poll every 3 seconds
}

function showTypingIndicator(userId, userName) {
    // Implementation for typing indicator
    const typingEl = document.getElementById('typingIndicator');
    if (typingEl) {
        typingEl.textContent = `${userName} is typing...`;
        typingEl.style.display = 'block';
        
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => {
            typingEl.style.display = 'none';
        }, 3000);
    }
}

// Form submission
document.getElementById('sendBtn')?.addEventListener('click', function() {
    sendMessage();
});

document.getElementById('messageInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    const replyToId = document.getElementById('replyToId')?.value;
    
    if (!message && !replyToId) return;
    
    const formData = new FormData();
    formData.append('message', message);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    if (replyToId) formData.append('reply_to_id', replyToId);
    
    fetch(`/chat/${chatId}/send`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addMessage(data.message);
            input.value = '';
            cancelReply();
        }
    })
    .catch(error => console.error('Error:', error));
}

function addMessage(message) {
    // Check if message already exists to prevent duplicates
    const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
    if (existingMessage) {
        console.log('Message already exists, skipping duplicate', message.id);
        return;
    }
    
    const messagesDiv = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-wrapper ${message.sender_id == {{ $user->id }} ? 'message-sent' : 'message-received'}`;
    messageDiv.setAttribute('data-message-id', message.id);
    
    let content = '';
    if (message.message_type === 'voice') {
        content = `
            <div class="message-bubble voice-message">
                <div class="voice-player">
                    <button class="voice-play-btn" onclick="toggleAudio(this, '${message.voice_url}')">
                        <i class="fas fa-play"></i>
                    </button>
                    <div class="voice-waveform">
                        <div class="voice-duration">Voice message</div>
                    </div>
                </div>
                <div class="message-time">${message.formatted_time}</div>
            </div>
        `;
    } else {
        let mediaHtml = '';
        if (message.media_path) {
            if (message.media_type === 'image') {
                mediaHtml = `<div class="message-media"><img src="${message.media_path}" class="message-image"></div>`;
            } else if (message.media_type === 'video') {
                mediaHtml = `<div class="message-media"><video controls class="message-video"><source src="${message.media_path}"></video></div>`;
            }
        }
        
        content = `
            <div class="message-bubble">
                ${mediaHtml}
                ${message.message ? `<div class="message-text">${message.message}</div>` : ''}
                <div class="message-footer">
                    <span class="message-time">${message.formatted_time}</span>
                    ${message.sender_id == {{ $user->id }} ? '<i class="fas fa-check read-status"></i>' : ''}
                </div>
            </div>
        `;
    }
    
    messageDiv.innerHTML = content;
    messagesDiv.appendChild(messageDiv);
    scrollToBottom();
}

function scrollToBottom() {
    const messages = document.getElementById('chatMessages');
    messages.scrollTop = messages.scrollHeight;
}

// Emoji Picker
function toggleEmojiPicker() {
    const picker = document.getElementById('emojiPicker');
    picker.classList.toggle('show');
}

document.getElementById('emojiBtn')?.addEventListener('click', toggleEmojiPicker);

function insertEmoji(emoji) {
    const input = document.getElementById('messageInput');
    input.value += emoji;
    input.focus();
}

// Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('messengerSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('show');
}

function switchTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tab + 'Tab').classList.add('active');
    if (event && event.target) {
        event.target.closest('.tab-btn').classList.add('active');
    }
}

function scrollToMessage(messageId) {
    const message = document.querySelector(`[data-message-id="${messageId}"]`);
    if (message) {
        message.scrollIntoView({ behavior: 'smooth', block: 'center' });
        message.style.background = 'rgba(0, 132, 255, 0.1)';
        setTimeout(() => {
            message.style.background = '';
        }, 2000);
    }
}

// ==================== Voice/Video Call WebRTC Implementation ====================

let peerConnection = null;
let localStream = null;
let remoteStream = null;
let currentCallId = null;
let isCaller = false;
let callChannel = null;
let isVideoCall = false;
let isMicMuted = false;
let isCameraOn = false;

// Call UI elements (will be created dynamically)
let callModal = null;
let callAudio = null;
let localVideo = null;
let remoteVideo = null;

// Initialize WebRTC
function initializeWebRTC() {
    const configuration = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' }
        ]
    };
    
    peerConnection = new RTCPeerConnection(configuration);
    
    // Handle remote stream
    peerConnection.ontrack = (event) => {
        console.log('Received remote stream', event);
        remoteStream = event.streams[0];
        
        // Handle audio
        if (callAudio) {
            callAudio.srcObject = remoteStream;
        }
        
        // Handle video
        if (remoteVideo && event.track.kind === 'video') {
            remoteVideo.srcObject = remoteStream;
            remoteVideo.style.display = 'block';
        }
    };
    
    // Handle ICE candidates
    peerConnection.onicecandidate = (event) => {
        if (event.candidate && currentCallId) {
            fetch(`/call/${currentCallId}/ice-candidate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    candidate: JSON.stringify(event.candidate)
                })
            }).catch(err => console.error('Error sending ICE candidate:', err));
        }
    };
    
    // Handle connection state changes
    peerConnection.onconnectionstatechange = () => {
        console.log('Connection state:', peerConnection.connectionState);
        if (peerConnection.connectionState === 'failed' || peerConnection.connectionState === 'disconnected') {
            endCall();
        }
    };
}

// Start voice call
window.startVoiceCall = async function() {
    console.log('startVoiceCall called');
    isVideoCall = false;
    await initiateCall('voice');
}

// Start video call
window.startVideoCall = async function() {
    console.log('startVideoCall called');
    isVideoCall = true;
    await initiateCall('video');
}

// Initiate call (voice or video)
async function initiateCall(type) {
    try {
        const constraints = {
            audio: true,
            video: type === 'video'
        };
        
        localStream = await navigator.mediaDevices.getUserMedia(constraints);
        isCameraOn = type === 'video';
        
        initializeWebRTC();
        
        localStream.getTracks().forEach(track => {
            peerConnection.addTrack(track, localStream);
        });
        
        const offer = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offer);
        
        const response = await fetch('/call/initiate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                receiver_id: {{ $otherUser->id }},
                chat_id: chatId,
                type: type,
                offer: JSON.stringify(offer)
            })
        });
        
        const data = await response.json();
        if (data.success) {
            currentCallId = data.call_id;
            isCaller = true;
            
            if (window.Echo) {
                callChannel = window.Echo.private(`call.${currentCallId}`);
                
                callChannel.listen('.call.answer', (data) => {
                    handleCallAnswer(data);
                });
                
                callChannel.listen('.call.ice-candidate', (data) => {
                    handleIceCandidate(data);
                });
                
                callChannel.listen('.call.ended', (data) => {
                    handleCallEnded(data);
                });
            }
            
            showCallUI('calling', { type: type });
        } else {
            alert('Failed to start call: ' + (data.error || 'Unknown error'));
            stopLocalStream();
        }
    } catch (error) {
        console.error('Error starting call:', error);
        alert('Failed to start call. Please check your permissions.');
        stopLocalStream();
    }
}

// Handle incoming call offer
function handleIncomingCall(data) {
    console.log('handleIncomingCall called with data:', data);
    console.log('Full data object:', JSON.stringify(data, null, 2));
    
    currentCallId = data.callId || data.call_id;
    isCaller = false;
    
    if (!currentCallId) {
        console.error('No call ID in incoming call data:', data);
        alert('Invalid call data received. Please try again.');
        return;
    }
    
    if (data.offer) {
        window.pendingCallOffer = data.offer;
        console.log('Stored pending call offer, length:', data.offer.length);
    } else {
        console.error('No offer in incoming call data:', data);
        alert('Call offer data is missing. Please try again.');
        return;
    }
    
    const callType = data.type || 'voice';
    isVideoCall = callType === 'video';
    
    console.log('Incoming call - callId:', currentCallId, 'type:', callType, 'isVideoCall:', isVideoCall);
    
    // Show incoming call UI
    try {
        showCallUI('incoming', {
            caller_name: data.callerName || data.caller_name || '{{ $otherUser->info->name }}',
            type: callType
        });
        console.log('Call UI shown for incoming call');
    } catch (error) {
        console.error('Error showing call UI:', error);
        alert('Error displaying call. Please check console for details.');
    }
}

// Handle call answer
async function handleCallAnswer(data) {
    if (!data.accepted) {
        endCall();
        return;
    }
    
    try {
        const answer = JSON.parse(data.answer);
        await peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
        const callType = isVideoCall ? 'video' : 'voice';
        showCallUI('active', { type: callType });
        
        setTimeout(() => {
            const callControls = document.getElementById('callControls');
            if (callControls) {
                callControls.style.display = 'flex';
                callControls.style.visibility = 'visible';
            }
            updateActiveCallUI(callType);
        }, 500);
    } catch (error) {
        console.error('Error handling answer:', error);
        endCall();
    }
}

// Answer incoming call
window.answerCall = async function(accepted = true) {
    if (!accepted) {
        await fetch(`/call/${currentCallId}/answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                answer: '',
                accepted: false
            })
        });
        endCall();
        return;
    }
    
    try {
        if (!window.pendingCallOffer) {
            console.error('No pending call offer found');
            alert('Call offer not found. Please try again.');
            endCall();
            return;
        }
        
        let offerData;
        try {
            offerData = JSON.parse(window.pendingCallOffer);
        } catch (e) {
            console.error('Error parsing offer:', e);
            alert('Invalid call offer. Please try again.');
            endCall();
            return;
        }
        
        const isVideo = offerData.sdp && offerData.sdp.includes('m=video');
        isVideoCall = isVideo;
        isCameraOn = isVideo;
        
        console.log('Answering call - isVideo:', isVideo);
        
        const constraints = {
            audio: true,
            video: isVideo
        };
        
        try {
            localStream = await navigator.mediaDevices.getUserMedia(constraints);
            console.log('Got user media, tracks:', localStream.getTracks().map(t => ({kind: t.kind, enabled: t.enabled})));
        } catch (mediaError) {
            console.error('Error getting user media:', mediaError);
            alert('Failed to access ' + (isVideo ? 'camera/microphone' : 'microphone') + '. Please check permissions.');
            endCall();
            return;
        }
        
        initializeWebRTC();
        
        localStream.getTracks().forEach(track => {
            peerConnection.addTrack(track, localStream);
        });
        
        const offer = JSON.parse(window.pendingCallOffer);
        await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
        window.pendingCallOffer = null;
        
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        
        console.log('Created answer, sending to server');
        
        const response = await fetch(`/call/${currentCallId}/answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                answer: JSON.stringify(answer),
                accepted: true
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Failed to send answer to server');
        }
        
        if (window.Echo) {
            callChannel = window.Echo.private(`call.${currentCallId}`);
            
            callChannel.listen('.call.ice-candidate', (data) => {
                handleIceCandidate(data);
            });
            
            callChannel.listen('.call.ended', (data) => {
                handleCallEnded(data);
            });
        }
        
        const callType = isVideo ? 'video' : 'voice';
        showCallUI('active', { type: callType });
        
        const showControls = () => {
            const callControls = document.getElementById('callControls');
            if (callControls) {
                callControls.style.display = 'flex';
                callControls.style.visibility = 'visible';
                callControls.style.opacity = '1';
                callControls.setAttribute('style', 'display: flex !important; visibility: visible !important; opacity: 1 !important;');
            }
            updateActiveCallUI(callType);
            
            if (localVideo && localStream) {
                localVideo.srcObject = localStream;
                if (isVideo) {
                    localVideo.style.display = 'block';
                }
            }
        };
        
        showControls();
        setTimeout(showControls, 100);
        setTimeout(showControls, 300);
        setTimeout(showControls, 600);
        setTimeout(showControls, 1000);
        setTimeout(showControls, 2000);
    } catch (error) {
        console.error('Error answering call:', error);
        alert('Failed to answer call: ' + error.message);
        endCall();
    }
}

// Handle ICE candidate
async function handleIceCandidate(data) {
    if (data.user_id !== {{ $user->id }}) {
        try {
            const candidate = JSON.parse(data.candidate);
            await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
        } catch (error) {
            console.error('Error adding ICE candidate:', error);
        }
    }
}

// Handle call ended
function handleCallEnded(data) {
    endCall();
    alert('Call ended by the other party');
}

// End call
window.endCall = async function() {
    if (currentCallId) {
        try {
            await fetch(`/call/${currentCallId}/end`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        } catch (error) {
            console.error('Error ending call:', error);
        }
    }
    
    if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
    }
    
    stopLocalStream();
    
    if (callChannel) {
        callChannel.stopListening('.call.answer');
        callChannel.stopListening('.call.ice-candidate');
        callChannel.stopListening('.call.ended');
        callChannel = null;
    }
    
    hideCallUI();
    stopCallTimer();
    
    currentCallId = null;
    isCaller = false;
    isVideoCall = false;
    isMicMuted = false;
    isCameraOn = false;
}

// Stop local stream
function stopLocalStream() {
    if (localStream) {
        localStream.getTracks().forEach(track => track.stop());
        localStream = null;
    }
}

// Show call UI
function showCallUI(state, data = null) {
    if (!callModal) {
        createCallUI();
    }
    
    const modal = document.getElementById('callModal');
    const callingDiv = document.getElementById('callingState');
    const incomingDiv = document.getElementById('incomingState');
    const activeDiv = document.getElementById('activeState');
    
    callingDiv.style.display = 'none';
    incomingDiv.style.display = 'none';
    activeDiv.style.display = 'none';
    
    switch(state) {
        case 'calling':
            callingDiv.style.display = 'block';
            modal.style.display = 'flex';
            break;
        case 'incoming':
            console.log('Showing incoming call UI');
            incomingDiv.style.display = 'block';
            incomingDiv.style.flexDirection = 'column';
            if (data) {
                if (data.caller_name) {
                    const nameEl = document.getElementById('incomingCallerName');
                    if (nameEl) {
                        nameEl.textContent = data.caller_name;
                    }
                }
                if (data.type) {
                    const typeLabel = document.getElementById('incomingCallType');
                    if (typeLabel) {
                        typeLabel.textContent = data.type === 'video' ? 'Video call' : 'Voice call';
                    }
                }
            }
            modal.style.display = 'flex';
            console.log('Incoming call modal displayed');
            break;
        case 'active':
            activeDiv.style.display = 'flex';
            modal.style.display = 'flex';
            startCallTimer();
            const callType = data?.type || (isVideoCall ? 'video' : 'voice');
            updateActiveCallUI(callType);
            // Force show controls immediately
            setTimeout(() => {
                const callControls = document.getElementById('callControls');
                if (callControls) {
                    callControls.style.display = 'flex';
                    callControls.style.visibility = 'visible';
                }
                const micBtn = document.getElementById('micBtn');
                if (micBtn) {
                    micBtn.style.display = 'flex';
                    micBtn.style.visibility = 'visible';
                }
                const endCallBtn = document.querySelector('.end-call-main-btn');
                if (endCallBtn) {
                    endCallBtn.style.display = 'flex';
                    endCallBtn.style.visibility = 'visible';
                }
                updateActiveCallUI(callType);
            }, 100);
            break;
    }
}

// Hide call UI
function hideCallUI() {
    const modal = document.getElementById('callModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Create call UI
function createCallUI() {
    const modal = document.createElement('div');
    modal.id = 'callModal';
    modal.className = 'call-modal';
    modal.innerHTML = `
        <div class="call-container">
            <!-- Calling State -->
            <div id="callingState" class="call-state">
                <div class="call-avatar">
                    <img src="{{ $otherUser->info->getPicture() }}" alt="{{ $otherUser->info->name }}">
                </div>
                <h3>Calling {{ $otherUser->info->name }}...</h3>
                <button class="call-btn end-call-btn" onclick="endCall()">
                    <i class="fas fa-phone-slash"></i>
                </button>
            </div>
            
            <!-- Incoming Call State -->
            <div id="incomingState" class="call-state">
                <div class="call-header">
                    <div id="incomingCallType" class="call-type">Voice call</div>
                </div>
                <div class="call-avatar-container">
                    <div class="call-avatar">
                        <img src="{{ $otherUser->info->getPicture() }}" alt="{{ $otherUser->info->name }}">
                    </div>
                    <div class="call-name" id="incomingCallerName">{{ $otherUser->info->name }}</div>
                    <div class="call-number">{{ $otherUser->info->phone ?? 'No phone number' }}</div>
                </div>
                <div class="call-actions">
                    <button class="call-btn accept-call-btn" onclick="answerCall(true)">
                        <i class="fas fa-phone"></i>
                    </button>
                    <button class="call-btn reject-call-btn" onclick="answerCall(false)">
                        <i class="fas fa-phone-slash"></i>
                    </button>
                </div>
            </div>
            
            <!-- Active Call State -->
            <div id="activeState" class="call-state">
                <div class="call-participants">
                    <div class="participant-avatar">
                        <img src="{{ $otherUser->info->getPicture() }}" alt="{{ $otherUser->info->name }}">
                    </div>
                </div>
                
                <div class="call-info-section">
                    <div class="call-status-text">In Call with</div>
                    <div class="call-participant-name">{{ $otherUser->info->name }} {{ $otherUser->info->surname }}</div>
                    <div id="callDuration" class="call-duration-display">00:00</div>
                </div>
                
                <div class="call-video-container" id="videoContainer" style="display: none;">
                    <video id="remoteVideo" autoplay playsinline></video>
                    <video id="localVideo" autoplay playsinline muted></video>
                </div>
                
                <div class="call-main-controls" id="callControls">
                    <button id="micBtn" class="main-control-btn mute-btn" onclick="toggleMic()" title="Mute">
                        <i class="fas fa-microphone"></i>
                        <span class="control-label">Mute</span>
                    </button>
                    <button class="main-control-btn end-call-main-btn" onclick="endCall()" title="End call">
                        <i class="fas fa-phone-slash"></i>
                    </button>
                    <button id="videoBtn" class="main-control-btn video-toggle-btn" onclick="toggleVideo()" title="Video" style="display: none;">
                        <i class="fas fa-video"></i>
                        <span class="control-label">Video</span>
                    </button>
                </div>
                
                <audio id="callAudio" autoplay></audio>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    callAudio = document.getElementById('callAudio');
    localVideo = document.getElementById('localVideo');
    remoteVideo = document.getElementById('remoteVideo');
    
    if (localVideo) {
        const updateLocalVideo = () => {
            if (localStream && isVideoCall) {
                localVideo.srcObject = localStream;
                localVideo.style.display = isCameraOn ? 'block' : 'none';
            } else {
                localVideo.style.display = 'none';
            }
        };
        updateLocalVideo();
        setInterval(updateLocalVideo, 500);
    }
}

// Toggle microphone
window.toggleMic = function() {
    console.log('toggleMic called');
    if (localStream) {
        const audioTracks = localStream.getAudioTracks();
        if (audioTracks.length > 0) {
            isMicMuted = !isMicMuted;
            audioTracks[0].enabled = !isMicMuted;
            
            const micBtn = document.getElementById('micBtn');
            if (micBtn) {
                if (isMicMuted) {
                    micBtn.classList.add('muted');
                    micBtn.querySelector('i').classList.remove('fa-microphone');
                    micBtn.querySelector('i').classList.add('fa-microphone-slash');
                } else {
                    micBtn.classList.remove('muted');
                    micBtn.querySelector('i').classList.remove('fa-microphone-slash');
                    micBtn.querySelector('i').classList.add('fa-microphone');
                }
            }
        }
    } else {
        console.warn('No local stream available for mic toggle');
    }
}

// Toggle video (camera on/off)
window.toggleVideo = async function() {
    if (isVideoCall) {
        toggleCamera();
    } else {
        alert('Video is only available for video calls. Start a video call to use this feature.');
    }
}

// Toggle camera
async function toggleCamera() {
    if (localStream && isVideoCall) {
        const videoTracks = localStream.getVideoTracks();
        if (videoTracks.length > 0) {
            isCameraOn = !isCameraOn;
            videoTracks[0].enabled = isCameraOn;
            
            const videoBtn = document.getElementById('videoBtn');
            if (videoBtn) {
                if (!isCameraOn) {
                    videoBtn.classList.add('off');
                } else {
                    videoBtn.classList.remove('off');
                }
            }
            
            if (localVideo) {
                localVideo.style.display = isCameraOn ? 'block' : 'none';
            }
        }
    }
}

// Update active call UI
function updateActiveCallUI(type) {
    const callControls = document.getElementById('callControls');
    if (callControls) {
        callControls.style.display = 'flex';
        callControls.style.visibility = 'visible';
    }
    
    // Show/hide video elements
    const videoContainer = document.getElementById('videoContainer');
    if (type === 'video') {
        if (videoContainer) videoContainer.style.display = 'block';
        if (localVideo) localVideo.style.display = 'block';
        if (remoteVideo) remoteVideo.style.display = 'block';
        const videoBtn = document.getElementById('videoBtn');
        if (videoBtn) videoBtn.style.display = 'flex';
    } else {
        if (videoContainer) videoContainer.style.display = 'none';
        if (localVideo) localVideo.style.display = 'none';
        if (remoteVideo) remoteVideo.style.display = 'none';
        const videoBtn = document.getElementById('videoBtn');
        if (videoBtn) videoBtn.style.display = 'none';
    }
    
    // Always show mic and end call buttons
    const micBtn = document.getElementById('micBtn');
    if (micBtn) {
        micBtn.style.display = 'flex';
        micBtn.style.visibility = 'visible';
    }
    
    const endCallBtn = document.querySelector('.end-call-main-btn');
    if (endCallBtn) {
        endCallBtn.style.display = 'flex';
        endCallBtn.style.visibility = 'visible';
    }
}

// Listen for incoming calls via WebSocket
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up call listener for user {{ $user->id }}');
    
    function setupCallListener() {
        if (window.Echo) {
            console.log('Echo is available, setting up listener');
            try {
                const userChannel = window.Echo.private(`user.{{ $user->id }}`);
                console.log('User channel created:', userChannel);
                
                userChannel.listen('.call.offer', (data) => {
                    console.log('Incoming call offer received:', data);
                    handleIncomingCall(data);
                });
                
                // Also listen for connection status
                userChannel.subscribed(() => {
                    console.log('Subscribed to user channel for incoming calls');
                });
                
                userChannel.error((error) => {
                    console.error('Error subscribing to user channel:', error);
                });
            } catch (error) {
                console.error('Error setting up call listener:', error);
            }
        } else {
            console.warn('Echo is not available, retrying in 1 second...');
            setTimeout(setupCallListener, 1000);
        }
    }
    
    // Try immediately and with delays
    setupCallListener();
    setTimeout(setupCallListener, 1000);
    setTimeout(setupCallListener, 2000);
    setTimeout(setupCallListener, 3000);
});

// Call duration timer
let callTimerInterval = null;
let callStartTime = null;

function startCallTimer() {
    callStartTime = Date.now();
    callTimerInterval = setInterval(() => {
        const duration = Math.floor((Date.now() - callStartTime) / 1000);
        const minutes = Math.floor(duration / 60);
        const seconds = duration % 60;
        const formatted = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        const durationEl = document.getElementById('callDuration');
        if (durationEl) {
            durationEl.textContent = formatted;
        }
    }, 1000);
}

function stopCallTimer() {
    if (callTimerInterval) {
        clearInterval(callTimerInterval);
        callTimerInterval = null;
    }
    callStartTime = null;
}

// Media Upload
function handleMediaUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    const formData = new FormData();
    formData.append('media', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    formData.append('media_type', file.type.startsWith('image/') ? 'image' : 'video');
    
    fetch(`/chat/${chatId}/send`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addMessage(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Voice Message
document.getElementById('voiceMessageButton')?.addEventListener('click', function() {
    if (!isRecordingVoice) {
        startVoiceRecording();
    } else {
        stopVoiceRecording();
    }
});

function startVoiceRecording() {
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];
            
            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };
            
            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                sendVoiceMessage(audioBlob);
                stream.getTracks().forEach(track => track.stop());
            };
            
            mediaRecorder.start();
            isRecordingVoice = true;
            this.classList.add('recording');
        })
        .catch(error => console.error('Error accessing microphone:', error));
}

function stopVoiceRecording() {
    if (mediaRecorder && isRecordingVoice) {
        mediaRecorder.stop();
        isRecordingVoice = false;
        document.getElementById('voiceMessageButton').classList.remove('recording');
    }
}

function sendVoiceMessage(audioBlob) {
    const formData = new FormData();
    formData.append('voice_message', audioBlob, 'voice.wav');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch(`/chat/${chatId}/send-voice`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addMessage(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Reply
function cancelReply() {
    document.getElementById('replyPreview').style.display = 'none';
    document.getElementById('replyToId').value = '';
}

// Media Modal
function openMediaModal(path, type) {
    const modal = document.getElementById('mediaModal');
    const img = document.getElementById('mediaModalImg');
    const video = document.getElementById('mediaModalVideo');
    
    img.style.display = 'none';
    video.style.display = 'none';
    
    if (type === 'image') {
        img.src = path;
        img.style.display = 'block';
    } else {
        video.src = path;
        video.style.display = 'block';
    }
    
    modal.classList.add('show');
}

function closeMediaModal() {
    document.getElementById('mediaModal').classList.remove('show');
}

// Audio toggle
function toggleAudio(btn, url) {
    const audio = new Audio(url);
    if (btn.querySelector('.fa-play')) {
        audio.play();
        btn.innerHTML = '<i class="fas fa-pause"></i>';
        audio.onended = () => {
            btn.innerHTML = '<i class="fas fa-play"></i>';
        };
    } else {
        audio.pause();
        btn.innerHTML = '<i class="fas fa-play"></i>';
    }
}

// Message Search
const searchInput = document.getElementById('messageSearchInput');
if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        const results = document.getElementById('searchResults');
        
        clearTimeout(searchTimeout);
        
        if (query.length < 1) {
            results.innerHTML = '';
            return;
        }
        
        // Debounce search
        searchTimeout = setTimeout(() => {
            results.innerHTML = '<p style="text-align: center; color: #65676b; padding: 20px;">Searching...</p>';
            
            const url = `/chat/${chatId}/search?query=${encodeURIComponent(query)}`;
            console.log('Searching:', url);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Search response status:', response.status);
                console.log('Search response headers:', response.headers);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Search error response:', text);
                        let errorMsg = 'Search failed';
                        try {
                            const errorData = JSON.parse(text);
                            errorMsg = errorData.error || errorData.message || errorMsg;
                        } catch (e) {
                            errorMsg = text || errorMsg;
                        }
                        throw new Error(errorMsg);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Search results:', data);
                
                if (data.error) {
                    results.innerHTML = `<p style="text-align: center; color: #f02849; padding: 20px;">${data.error}</p>`;
                    return;
                }
                
                if (data.messages && Array.isArray(data.messages) && data.messages.length > 0) {
                    results.innerHTML = data.messages.map(msg => `
                        <div class="search-result-item" onclick="scrollToMessage(${msg.id})">
                            <div class="search-result-sender">${msg.sender_name || 'User'}</div>
                            <div class="search-result-text">${msg.message || '(No text)'}</div>
                            <div class="search-result-time">${msg.formatted_time || ''}</div>
                        </div>
                    `).join('');
                } else {
                    results.innerHTML = '<p style="text-align: center; color: #65676b; padding: 20px;">No messages found</p>';
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                console.error('Error stack:', error.stack);
                results.innerHTML = `<p style="text-align: center; color: #f02849; padding: 20px;">Error: ${error.message || 'Please try again'}</p>`;
            });
        }, 500);
    });
}

// Initialize
scrollToBottom();
</script>

<style>
.call-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(240, 242, 245, 0.95);
    backdrop-filter: blur(10px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    color: #050505;
}

.call-container {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 40px 20px;
    max-width: 100%;
    background: transparent;
}

.call-state {
    display: none;
    width: 100%;
    height: 100%;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
}

.call-header {
    text-align: center;
    margin-bottom: 20px;
}

.call-type {
    font-size: 16px;
    opacity: 0.8;
    margin-bottom: 8px;
}

.call-duration {
    font-size: 18px;
    font-weight: 500;
}

.call-participants {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-top: 60px;
    margin-bottom: 20px;
}

.participant-avatar {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #4CAF50;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.participant-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.call-info-section {
    text-align: center;
    margin-bottom: 40px;
}

.call-status-text {
    font-size: 14px;
    color: #4CAF50;
    font-weight: 500;
    margin-bottom: 8px;
}

.call-participant-name {
    font-size: 28px;
    font-weight: 600;
    color: #050505;
    margin-bottom: 8px;
}

.call-duration-display {
    font-size: 18px;
    color: #65676b;
    font-weight: 500;
}

.call-video-container {
    position: relative;
    width: 100%;
    height: 100%;
    flex: 1;
    margin-bottom: 20px;
    border-radius: 12px;
    overflow: hidden;
    background: #000;
}

#remoteVideo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

#localVideo {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 120px;
    height: 160px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid white;
    background: #000;
}

.call-avatar-container {
    text-align: center;
    margin-bottom: 30px;
}

.call-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin: 0 auto 20px;
    overflow: hidden;
    border: 3px solid #4CAF50;
    position: relative;
}

.call-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.call-status {
    font-size: 14px;
    opacity: 0.7;
    margin-bottom: 8px;
}

.call-name {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 4px;
}

.call-number {
    font-size: 16px;
    opacity: 0.8;
}

.call-state h3 {
    margin: 10px 0;
    font-size: 24px;
}

.call-state p {
    margin: 10px 0 30px;
    opacity: 0.9;
}

.call-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-bottom: 20px;
}

.call-controls {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-bottom: 20px;
}

.control-btn {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.control-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.control-btn.active {
    background: rgba(76, 175, 80, 0.3);
}

.control-btn.muted {
    background: rgba(244, 67, 54, 0.3);
}

.control-btn.off {
    background: rgba(158, 158, 158, 0.3);
}

.call-main-controls {
    display: flex;
    gap: 30px;
    justify-content: center;
    align-items: center;
    margin-bottom: 40px;
    padding: 0 20px;
}

.main-control-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: all 0.2s;
    padding: 12px;
}

.main-control-btn .control-label {
    font-size: 12px;
    color: #65676b;
    font-weight: 500;
    margin-top: 4px;
}

.mute-btn, .video-toggle-btn {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #e4e6eb;
    color: #050505;
    font-size: 24px;
}

.mute-btn:hover, .video-toggle-btn:hover {
    background: #d0d2d6;
    transform: scale(1.05);
}

.mute-btn.muted {
    background: #f44336;
    color: white;
}

.mute-btn.muted .control-label {
    color: #f44336;
}

.end-call-main-btn {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #f44336;
    color: white;
    font-size: 28px;
    box-shadow: 0 4px 12px rgba(244, 67, 54, 0.4);
}

.end-call-main-btn:hover {
    background: #d32f2f;
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(244, 67, 54, 0.5);
}

.call-btn {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    border: none;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.call-btn:hover {
    transform: scale(1.1);
}

.accept-call-btn {
    background: #4CAF50;
    color: white;
}

.reject-call-btn {
    background: #f44336;
    color: white;
}

.end-call-btn {
    background: #f44336;
    color: white;
    width: 64px;
    height: 64px;
    margin: 0 auto;
}

/* Call Record in Chat */
.call-record-wrapper {
    display: flex;
    justify-content: center;
    margin: 10px 0;
    padding: 0 20px;
}

.call-record {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 20px;
    max-width: 300px;
    font-size: 0.875rem;
}

.call-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(0, 132, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0084ff;
    flex-shrink: 0;
}

.call-icon i {
    font-size: 14px;
}

.call-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.call-status {
    font-weight: 500;
    color: #050505;
}

.call-time {
    font-size: 0.75rem;
    color: #65676b;
}

.call-record.missed .call-icon {
    background: rgba(244, 67, 54, 0.1);
    color: #f44336;
}

.call-record.rejected .call-icon {
    background: rgba(158, 158, 158, 0.1);
    color: #9e9e9e;
}

/* Call Modal Styles */
.call-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(240, 242, 245, 0.95);
    backdrop-filter: blur(10px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    color: #050505;
}

.call-container {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 40px 20px;
    max-width: 100%;
    background: transparent;
}

.call-state {
    display: none;
    width: 100%;
    height: 100%;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
}

.call-participants {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-top: 60px;
    margin-bottom: 20px;
}

.participant-avatar {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #4CAF50;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.participant-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.call-info-section {
    text-align: center;
    margin-bottom: 40px;
}

.call-status-text {
    font-size: 14px;
    color: #4CAF50;
    font-weight: 500;
    margin-bottom: 8px;
}

.call-participant-name {
    font-size: 28px;
    font-weight: 600;
    color: #050505;
    margin-bottom: 8px;
}

.call-duration-display {
    font-size: 18px;
    color: #65676b;
    font-weight: 500;
}

.call-header {
    text-align: center;
    margin-bottom: 20px;
}

.call-type {
    font-size: 16px;
    opacity: 0.8;
    margin-bottom: 8px;
}

.call-duration {
    font-size: 18px;
    font-weight: 500;
}

.call-video-container {
    position: relative;
    width: 100%;
    height: 100%;
    flex: 1;
    margin-bottom: 20px;
    border-radius: 12px;
    overflow: hidden;
    background: #000;
}

#remoteVideo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

#localVideo {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 120px;
    height: 160px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid white;
    background: #000;
}

.call-avatar-container {
    text-align: center;
    margin-bottom: 30px;
}

.call-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin: 0 auto 20px;
    overflow: hidden;
    border: 3px solid #4CAF50;
    position: relative;
}

.call-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.call-status {
    font-size: 14px;
    opacity: 0.7;
    margin-bottom: 8px;
}

.call-name {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 4px;
}

.call-number {
    font-size: 16px;
    opacity: 0.8;
}

.call-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-bottom: 20px;
}

.call-controls {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-bottom: 20px;
}

.control-btn {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.control-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.control-btn.active {
    background: rgba(76, 175, 80, 0.3);
}

.control-btn.muted {
    background: rgba(244, 67, 54, 0.3);
}

.control-btn.off {
    background: rgba(158, 158, 158, 0.3);
}

.call-btn {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    border: none;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.call-btn:hover {
    transform: scale(1.1);
}

.accept-call-btn {
    background: #4CAF50;
    color: white;
}

.reject-call-btn {
    background: #f44336;
    color: white;
}

.end-call-btn {
    background: #f44336;
    color: white;
    width: 64px;
    height: 64px;
    margin: 0 auto;
}
</style>

@endsection

