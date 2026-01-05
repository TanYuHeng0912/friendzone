@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <a href="{{ route('chat.show', $chat->id) }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        Media Gallery
                    </h2>
                    <p class="text-muted mb-0">Shared media with {{ $otherUser->info->name }}</p>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active" onclick="filterMedia('all')">
                        <i class="fas fa-th"></i> All
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="filterMedia('images')">
                        <i class="fas fa-image"></i> Images
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="filterMedia('videos')">
                        <i class="fas fa-video"></i> Videos
                    </button>
                </div>
            </div>

            @if($mediaMessages->isEmpty())
                <div class="text-center empty-state py-5">
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <h4>No media shared yet</h4>
                    <p class="text-muted">Start sharing images and videos in your chat!</p>
                    <a href="{{ route('chat.show', $chat->id) }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Chat
                    </a>
                </div>
            @else
                <!-- Media Grid -->
                <div id="mediaGrid" class="media-grid">
                    @foreach($mediaMessages as $message)
                        <div class="media-item" data-type="{{ $message->media_type }}">
                            @if($message->media_type === 'image' || $message->media_type === 'gif')
                                <img src="{{ asset('storage/' . $message->media_path) }}" 
                                     alt="Shared image"
                                     class="media-thumbnail"
                                     onclick="openMediaModal('{{ asset('storage/' . $message->media_path) }}', 'image', '{{ $message->sender->info->name }}', '{{ $message->created_at->format('M d, Y H:i') }}')">
                                <div class="media-overlay">
                                    <i class="fas fa-image"></i>
                                </div>
                            @elseif($message->media_type === 'video')
                                <video class="media-thumbnail" 
                                       @if($message->media_thumbnail)
                                           poster="{{ asset('storage/' . $message->media_thumbnail) }}"
                                       @endif
                                       onclick="openMediaModal('{{ asset('storage/' . $message->media_path) }}', 'video', '{{ $message->sender->info->name }}', '{{ $message->created_at->format('M d, Y H:i') }}')">
                                    <source src="{{ asset('storage/' . $message->media_path) }}" type="video/mp4">
                                </video>
                                <div class="media-overlay">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                            @endif
                            <div class="media-info">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> {{ $message->sender->info->name }}
                                    <i class="fas fa-clock ml-2"></i> {{ $message->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Media Modal -->
<div id="mediaModal" class="media-modal" onclick="closeMediaModal()">
    <span class="media-modal-close" onclick="closeMediaModal()">&times;</span>
    <div class="media-modal-content-wrapper">
        <div id="modalMediaContent"></div>
        <div class="media-modal-info">
            <p id="modalSender" class="mb-1"></p>
            <small id="modalTime" class="text-muted"></small>
        </div>
    </div>
</div>

<style>
    .empty-state {
        background: var(--card-bg);
        border-radius: var(--border-radius-md);
        padding: 60px 20px;
    }

    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .media-item {
        position: relative;
        border-radius: var(--border-radius-md);
        overflow: hidden;
        background: var(--card-bg);
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }

    .media-item:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .media-thumbnail {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    .media-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
        color: white;
        font-size: 2rem;
    }

    .media-item:hover .media-overlay {
        opacity: 1;
    }

    .media-info {
        padding: 10px;
        background: var(--card-bg);
    }

    .media-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.95);
        cursor: pointer;
    }

    .media-modal-content-wrapper {
        margin: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        max-width: 90%;
        max-height: 90%;
        margin-top: 5%;
    }

    .media-modal-content-wrapper img,
    .media-modal-content-wrapper video {
        max-width: 100%;
        max-height: 80vh;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }

    .media-modal-content-wrapper video {
        cursor: pointer;
    }

    .media-modal-info {
        margin-top: 20px;
        text-align: center;
        color: white;
    }

    .media-modal-close {
        position: absolute;
        top: 20px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
    }

    .media-modal-close:hover {
        color: #bbb;
    }

    .btn-group .btn.active {
        background: var(--primary-gradient);
        color: white;
        border-color: var(--primary-color);
    }

    @media (max-width: 768px) {
        .media-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        .media-thumbnail {
            height: 150px;
        }
    }
</style>

<script>
    function filterMedia(type) {
        const items = document.querySelectorAll('.media-item');
        const buttons = document.querySelectorAll('.btn-group .btn');
        
        // Update active button
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // Filter items
        items.forEach(item => {
            if (type === 'all') {
                item.style.display = 'block';
            } else if (type === 'images') {
                item.style.display = item.dataset.type === 'image' || item.dataset.type === 'gif' ? 'block' : 'none';
            } else if (type === 'videos') {
                item.style.display = item.dataset.type === 'video' ? 'block' : 'none';
            }
        });
    }

    function openMediaModal(mediaPath, mediaType, senderName, time) {
        event.stopPropagation();
        const modal = document.getElementById('mediaModal');
        const content = document.getElementById('modalMediaContent');
        const sender = document.getElementById('modalSender');
        const timeEl = document.getElementById('modalTime');
        
        if (mediaType === 'image') {
            content.innerHTML = `<img src="${mediaPath}" alt="Media">`;
        } else if (mediaType === 'video') {
            content.innerHTML = `<video controls autoplay><source src="${mediaPath}" type="video/mp4"></video>`;
        }
        
        sender.textContent = `Shared by ${senderName}`;
        timeEl.textContent = time;
        
        modal.style.display = 'flex';
    }

    function closeMediaModal() {
        document.getElementById('mediaModal').style.display = 'none';
        // Stop video playback
        const video = document.querySelector('#modalMediaContent video');
        if (video) {
            video.pause();
            video.currentTime = 0;
        }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMediaModal();
        }
    });
</script>
@endsection

