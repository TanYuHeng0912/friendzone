@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="row align-items-center">
                    <div class="col-1">
                        <a href="{{ route('chat.index') }}" class="btn btn-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="col-2">
                        <img src="{{ $otherUser->info->getPicture() }}" 
                             alt="{{ $otherUser->info->name }}"
                             class="chat-header-avatar">
                    </div>
                    <div class="col-9">
                        <h4 class="mb-0">{{ $otherUser->info->name }} {{ $otherUser->info->surname }}</h4>
                        <small class="text-muted">{{ $otherUser->info->age }} years old</small>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
           <div class="chat-messages" id="chatMessages">
    @foreach($chat->messages as $message)
        <div class="message {{ $message->sender_id == $user->id ? 'message-sent' : 'message-received' }}"
             data-message-id="{{ $message->id }}">
            @if($message->message_type === 'voice')
                <!-- Voice Message Display -->
                <div class="message-content voice-message-content">
                    <div class="voice-player">
                        <button class="voice-play-btn" onclick="toggleAudio(this, '{{ asset('storage/' . $message->message) }}')">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="voice-waveform">
                            <div class="voice-duration">Voice Message</div>
                            <audio preload="metadata" style="display: none;">
                                <source src="{{ asset('storage/' . $message->message) }}" type="audio/wav">
                            </audio>
                        </div>
                    </div>
                    <small class="message-time">{{ $message->formatted_time }}</small>
                </div>
            @else
                <!-- Text Message Display -->
                <div class="message-content">
                    <p>{{ $message->message }}</p>
                    <small class="message-time">{{ $message->formatted_time }}</small>
                </div>
            @endif
        </div>
    @endforeach
</div>

            <!-- Message Input -->
<!-- Replace your existing chat-input div with this updated version -->
<div class="chat-input">
    <form id="messageForm" class="d-flex">
        @csrf
        <button type="button" 
                id="voiceButton" 
                class="btn btn-outline-secondary voice-btn"
                title="Speech to text">
            <i class="fas fa-microphone"></i>
        </button>
        <button type="button" 
                id="voiceMessageButton" 
                class="btn btn-outline-primary voice-message-btn"
                title="Send voice message">
            <i class="fas fa-microphone-alt"></i>
        </button>
        <input type="text" 
               id="messageInput" 
               class="form-control" 
               placeholder="Type, speak to text, or record voice message..." 
               maxlength="1000"
               required>
        <button type="submit" class="btn btn-primary ml-2">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div>
        </div>
    </div>
</div>

<style>
    .container-fluid {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .chat-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        padding: 15px 20px;
        flex-shrink: 0;
    }

    .chat-header-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f5f5f5;
        max-height: 70vh;
    }

    .message {
        margin-bottom: 15px;
        display: flex;
    }

    .message-sent {
        justify-content: flex-end;
    }

    .message-received {
        justify-content: flex-start;
    }

    .message-content {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 18px;
        position: relative;
    }

    .message-sent .message-content {
        background: #007bff;
        color: white;
    }

    .message-received .message-content {
        background: white;
        color: #333;
        border: 1px solid #e0e0e0;
    }

    .message-time {
        font-size: 11px;
        opacity: 0.7;
        display: block;
        margin-top: 5px;
    }

    .chat-input {
        padding: 15px 20px;
        background: white;
        border-top: 1px solid #e0e0e0;
        flex-shrink: 0;
    }

    .chat-input .form-control {
        border-radius: 25px;
        padding: 12px 20px;
    }

    .chat-input .btn {
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Scrollbar styling */
    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
   .voice-btn, .voice-message-btn {
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    transition: all 0.3s ease;
    border: 2px solid #6c757d;
    background-color: white;
}

.voice-btn:hover {
    background-color: #f8f9fa;
    transform: scale(1.05);
}

.voice-message-btn {
    border-color: #007bff;
}

.voice-message-btn:hover {
    background-color: #e7f1ff;
    transform: scale(1.05);
}

.voice-btn.listening {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
    animation: pulse 1.5s infinite;
}

.voice-message-btn.recording {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
    animation: pulse-recording 1s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

@keyframes pulse-recording {
    0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.9);
        opacity: 1;
    }
    50% {
        box-shadow: 0 0 0 8px rgba(220, 53, 69, 0);
        opacity: 0.8;
    }
    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        opacity: 1;
    }
}

/* Update chat input form styling */
.chat-input form {
    align-items: center;
}

.chat-input .form-control {
    flex: 1;
    margin: 0;
}

/* Voice recognition indicator */
.voice-btn .fa-microphone-slash {
    animation: shake 0.5s infinite;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-2px); }
    75% { transform: translateX(2px); }
}

/* Voice message styling */
.voice-message {
    background: #e3f2fd;
    border: 1px solid #2196f3;
    border-radius: 18px;
    padding: 10px 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.voice-message audio {
    max-width: 200px;
    height: 30px;
}

.voice-message .voice-duration {
    font-size: 12px;
    color: #666;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .voice-btn, .voice-message-btn {
        width: 40px;
        height: 40px;
        margin-right: 5px;
    }
    
    .chat-input .btn {
        width: 40px;
        height: 40px;
    }
}
.voice-message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    border: none !important;
    min-width: 200px;
}

.message-received .voice-message-content {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
}

.voice-player {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 5px 0;
}

.voice-play-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.voice-play-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.voice-play-btn.playing {
    background: rgba(255, 255, 255, 0.4);
}

.voice-waveform {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.voice-duration {
    font-size: 12px;
    opacity: 0.9;
    font-weight: 500;
}

.voice-progress {
    height: 3px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
    overflow: hidden;
}

.voice-progress-bar {
    height: 100%;
    background: white;
    width: 0%;
    transition: width 0.1s ease;
    border-radius: 2px;
}

/* Hide file path display completely */
.voice-message-content p {
    display: none !important;
}

/* Animation for voice message */
@keyframes voice-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.voice-play-btn.playing {
    animation: voice-pulse 1s infinite;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const chatId = {{ $chat->id }};
    let lastMessageId = {{ $chat->messages->last()->id ?? 0 }};

    // Scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Add message to chat
 function addMessage(message) {
    const messageDiv = document.createElement('div');
    const isSent = message.sender_id == {{ $user->id }};
    
    messageDiv.className = `message ${isSent ? 'message-sent' : 'message-received'}`;
    messageDiv.setAttribute('data-message-id', message.id);
    
    let messageContent = '';
    if (message.message_type === 'voice') {
        messageContent = `
            <div class="message-content voice-message-content">
                <div class="voice-player">
                    <button class="voice-play-btn" onclick="toggleAudio(this, '${message.voice_url}')">
                        <i class="fas fa-play"></i>
                    </button>
                    <div class="voice-waveform">
                        <div class="voice-duration">Voice Message</div>
                    </div>
                </div>
                <audio preload="metadata" style="display: none;">
                    <source src="${message.voice_url}" type="audio/wav">
                </audio>
                <small class="message-time">${message.formatted_time}</small>
            </div>
        `;
    } else {
        messageContent = `
            <div class="message-content">
                <p>${message.message}</p>
                <small class="message-time">${message.formatted_time}</small>
            </div>
        `;
    }
    
    messageDiv.innerHTML = messageContent;
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // Update lastMessageId if it exists in your scope
    if (typeof lastMessageId !== 'undefined') {
        lastMessageId = Math.max(lastMessageId, message.id);
    }
}
    // Send message
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Disable input while sending
        messageInput.disabled = true;
        
        fetch(`/chat/${chatId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addMessage(data.message);
                messageInput.value = '';
            } else {
                alert('Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message');
        })
        .finally(() => {
            messageInput.disabled = false;
            messageInput.focus();
        });
    });

    // Poll for new messages
    function pollMessages() {
        fetch(`/chat/${chatId}/messages/${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        addMessage(message);
                    });
                }
            })
            .catch(error => console.error('Polling error:', error));
    }

    // Poll every 3 seconds
    setInterval(pollMessages, 3000);

    // Initial scroll to bottom and focus
    scrollToBottom();
    messageInput.focus();

    // Enter key to send
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });
});

// Move chatId to global scope - add this at the top of your script section
let chatId = {{ $chat->id }};

// Voice recognition variables (keep these global)
let recognition = null;
let isListening = false;
let mediaRecorder = null;
let audioChunks = [];
let isRecordingVoice = false;

document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    // chatId is now available globally - remove this line: let chatId = {{ $chat->id }};
    let lastMessageId = {{ $chat->messages->last()->id ?? 0 }};

    // Scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Add message to chat - UPDATE this function to handle voice messages
    function addMessage(message) {
        const messageDiv = document.createElement('div');
        const isSent = message.sender_id == {{ $user->id }};
        
        messageDiv.className = `message ${isSent ? 'message-sent' : 'message-received'}`;
        messageDiv.setAttribute('data-message-id', message.id);
        
        let messageContent = '';
        if (message.message_type === 'voice') {
            messageContent = `
                <div class="message-content voice-message">
                    <div class="voice-message-content">
                        <i class="fas fa-play-circle"></i>
                        <audio controls>
                            <source src="${message.voice_url}" type="audio/wav">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                    <small class="message-time">${message.formatted_time}</small>
                </div>
            `;
        } else {
            messageContent = `
                <div class="message-content">
                    <p>${message.message}</p>
                    <small class="message-time">${message.formatted_time}</small>
                </div>
            `;
        }
        
        messageDiv.innerHTML = messageContent;
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
        lastMessageId = Math.max(lastMessageId, message.id);
    }

    // Send message
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Disable input while sending
        messageInput.disabled = true;
        
        fetch(`/chat/${chatId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addMessage(data.message);
                messageInput.value = '';
            } else {
                alert('Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message');
        })
        .finally(() => {
            messageInput.disabled = false;
            messageInput.focus();
        });
    });

    // Poll for new messages - UPDATE to handle voice messages
    function pollMessages() {
        fetch(`/chat/${chatId}/messages/${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        addMessage(message);
                    });
                }
            })
            .catch(error => console.error('Polling error:', error));
    }

    // Poll every 3 seconds
    setInterval(pollMessages, 3000);

    // Initial scroll to bottom and focus
    scrollToBottom();
    messageInput.focus();

    // Enter key to send
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });

    // Initialize speech recognition
    initializeSpeechRecognition();
    
    // Add voice button event listener (speech-to-text)
    const voiceButton = document.getElementById('voiceButton');
    if (voiceButton) {
        voiceButton.addEventListener('click', toggleVoiceRecognition);
    }
    
    // Add voice message button event listener
    const voiceMessageButton = document.getElementById('voiceMessageButton');
    if (voiceMessageButton) {
        voiceMessageButton.addEventListener('click', toggleVoiceRecording);
    }
    
    // Stop listening when user starts typing
    messageInput.addEventListener('input', function() {
        if (isListening && recognition) {
            recognition.stop();
        }
    });
});

// Initialize speech recognition if supported
function initializeSpeechRecognition() {
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = 'en-US';
        recognition.maxAlternatives = 1;
        
        recognition.onstart = function() {
            console.log('Speech recognition started');
            isListening = true;
            updateVoiceButton();
        };
        
        recognition.onresult = function(event) {
            let interimTranscript = '';
            let finalTranscript = '';
            
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    finalTranscript += transcript;
                } else {
                    interimTranscript += transcript;
                }
            }
            
            // Update input field with interim or final results
            const messageInput = document.getElementById('messageInput');
            messageInput.value = (finalTranscript + interimTranscript).trim();
            
            // Auto-stop after getting final result
            if (finalTranscript && recognition) {
                setTimeout(() => {
                    if (recognition && isListening) {
                        recognition.stop();
                    }
                }, 1000);
            }
        };
        
        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            isListening = false;
            updateVoiceButton();
            
            if (event.error === 'not-allowed') {
                alert('Microphone access denied. Please allow microphone access to use voice input.');
            } else if (event.error === 'no-speech') {
                console.log('No speech detected, but continuing...');
            } else if (event.error === 'aborted') {
                console.log('Speech recognition aborted');
            } else {
                alert('Speech recognition error: ' + event.error);
            }
        };
        
        recognition.onend = function() {
            console.log('Speech recognition ended');
            isListening = false;
            updateVoiceButton();
        };
    }
}

// Toggle voice recognition (speech-to-text)
function toggleVoiceRecognition() {
    if (!recognition) {
        alert('Speech recognition is not supported in your browser.');
        return;
    }
    
    if (isListening) {
        console.log('Stopping speech recognition...');
        recognition.stop();
    } else {
        console.log('Starting speech recognition...');
        try {
            const messageInput = document.getElementById('messageInput');
            messageInput.value = ''; // Clear input field
            recognition.start();
        } catch (error) {
            console.error('Error starting speech recognition:', error);
            alert('Error starting voice input. Please try again.');
        }
    }
}

// Voice message recording functions
async function initializeVoiceRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        
        // Check what MIME types are supported
        let mimeType = 'audio/wav';
        if (MediaRecorder.isTypeSupported('audio/webm')) {
            mimeType = 'audio/webm';
        } else if (MediaRecorder.isTypeSupported('audio/mp4')) {
            mimeType = 'audio/mp4';
        } else if (MediaRecorder.isTypeSupported('audio/ogg')) {
            mimeType = 'audio/ogg';
        }
        
        console.log('Using MIME type:', mimeType);
        mediaRecorder = new MediaRecorder(stream, { mimeType: mimeType });
        
        mediaRecorder.ondataavailable = function(event) {
            audioChunks.push(event.data);
        };
        
        mediaRecorder.onstop = function() {
            const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
            sendVoiceMessage(audioBlob);
            audioChunks = [];
            
            // Stop all tracks to release microphone
            stream.getTracks().forEach(track => track.stop());
        };
        
        return true;
    } catch (error) {
        console.error('Error accessing microphone:', error);
        alert('Error accessing microphone for voice messages.');
        return false;
    }
}

function startVoiceRecording() {
    if (isRecordingVoice) return;
    
    initializeVoiceRecording().then(success => {
        if (success && mediaRecorder) {
            isRecordingVoice = true;
            audioChunks = [];
            mediaRecorder.start();
            updateVoiceMessageButton();
            
            // Auto-stop after 30 seconds
            setTimeout(() => {
                if (isRecordingVoice) {
                    stopVoiceRecording();
                }
            }, 30000);
        }
    });
}

function stopVoiceRecording() {
    if (!isRecordingVoice || !mediaRecorder) return;
    
    isRecordingVoice = false;
    mediaRecorder.stop();
    updateVoiceMessageButton();
}

function toggleVoiceRecording() {
    if (isRecordingVoice) {
        stopVoiceRecording();
    } else {
        startVoiceRecording();
    }
}

// FIXED: sendVoiceMessage function with proper error handling
async function sendVoiceMessage(audioBlob) {
    console.log('=== VOICE MESSAGE DEBUG START ===');
    console.log('Audio blob size:', audioBlob.size);
    console.log('Chat ID:', chatId);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token not found. Please refresh the page.');
        return;
    }
    
    const formData = new FormData();
    formData.append('voice_message', audioBlob, 'voice_message.wav');
    formData.append('_token', csrfToken.getAttribute('content'));
    
    try {
        const response = await fetch(`/chat/${chatId}/send-voice`, {
            method: 'POST',
            body: formData
        });
        
        const responseText = await response.text();
        
        if (responseText.trim().startsWith('<!doctype') || responseText.trim().startsWith('<!DOCTYPE')) {
            console.error('Server returned HTML error page');
            alert('Server Error. Check Laravel logs.');
            return;
        }
        
        const data = JSON.parse(responseText);
        
        if (data.success) {
            console.log('Success! Adding voice message to chat');
            
            // Create proper voice message display
            const messageDiv = document.createElement('div');
            const isSent = data.message.sender_id == {{ $user->id }};
            
            messageDiv.className = `message ${isSent ? 'message-sent' : 'message-received'}`;
            messageDiv.setAttribute('data-message-id', data.message.id);
            
            messageDiv.innerHTML = `
                <div class="message-content voice-message-content">
                    <div class="voice-player">
                        <button class="voice-play-btn" onclick="toggleAudio(this, '${data.message.voice_url}')">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="voice-waveform">
                            <div class="voice-duration">Voice Message</div>
                        </div>
                    </div>
                    <audio preload="metadata" style="display: none;">
                        <source src="${data.message.voice_url}" type="audio/wav">
                    </audio>
                    <small class="message-time">${data.message.formatted_time}</small>
                </div>
            `;
            
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
        } else {
            console.error('Server returned error:', data);
            alert('Failed to send voice message: ' + (data.error || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('Error sending voice message:', error);
        alert('Network error: ' + error.message);
    }
}

// Update voice button appearance
function updateVoiceButton() {
    const voiceButton = document.getElementById('voiceButton');
    if (voiceButton) {
        if (isListening) {
            voiceButton.innerHTML = '<i class="fas fa-microphone-slash text-danger"></i>';
            voiceButton.classList.add('listening');
        } else {
            voiceButton.innerHTML = '<i class="fas fa-microphone"></i>';
            voiceButton.classList.remove('listening');
        }
    }
}

// Update voice message button appearance
function updateVoiceMessageButton() {
    const voiceMessageButton = document.getElementById('voiceMessageButton');
    if (voiceMessageButton) {
        if (isRecordingVoice) {
            voiceMessageButton.innerHTML = '<i class="fas fa-stop text-danger"></i>';
            voiceMessageButton.classList.add('recording');
        } else {
            voiceMessageButton.innerHTML = '<i class="fas fa-microphone-alt"></i>';
            voiceMessageButton.classList.remove('recording');
        }
    }
}

function toggleAudio(button, audioUrl) {
    const icon = button.querySelector('i');
    const messageDiv = button.closest('.message');
    const audio = messageDiv.querySelector('audio');
    
    if (!audio.src) {
        audio.src = audioUrl;
    }
    
    if (audio.paused) {
        // Stop any other playing audio
        document.querySelectorAll('audio').forEach(a => {
            if (!a.paused) {
                a.pause();
                a.currentTime = 0;
            }
        });
        
        // Reset all play buttons
        document.querySelectorAll('.voice-play-btn').forEach(btn => {
            btn.classList.remove('playing');
            btn.querySelector('i').className = 'fas fa-play';
        });
        
        // Play this audio
        audio.play();
        button.classList.add('playing');
        icon.className = 'fas fa-pause';
        
        // Handle audio end
        audio.onended = function() {
            button.classList.remove('playing');
            icon.className = 'fas fa-play';
        };
        
    } else {
        // Pause audio
        audio.pause();
        audio.currentTime = 0;
        button.classList.remove('playing');
        icon.className = 'fas fa-play';
    }
}

</script>
@endsection