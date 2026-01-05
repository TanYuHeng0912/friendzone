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
            @foreach($chat->messages as $message)
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

// Voice Call
function startVoiceCall() {
    alert('Voice call feature coming soon!');
}

// Video Call
function startVideoCall() {
    alert('Video call feature coming soon!');
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
@endsection

