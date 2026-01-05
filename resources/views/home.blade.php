@extends('layouts.app')

@section('content')
    <div class="home-container">
        @if($user->info->profile_picture == null)
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-camera"></i>
                </div>
                <h1>Complete Your Profile</h1>
                <p>Add a profile picture to start matching!</p>
                <a href="{{ route('profile.showEditProfile') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </a>
            </div>
        @elseif($pictures == null)
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h1>No Matches Found</h1>
                <p>Try adjusting your search settings to find more people</p>
                <a href="{{ route('profile.updateSettings') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-cog"></i> Adjust Settings
                </a>
            </div>
        @else
            <div class="swipe-container">
                <!-- Swipe Direction Indicators -->
                <div class="swipe-indicators">
                    <div class="swipe-indicator swipe-indicator-left">
                        <div class="indicator-content">
                            <i class="fas fa-arrow-left"></i>
                            <span class="indicator-text">Swipe Left</span>
                            <span class="indicator-action">Dislike</span>
                        </div>
                    </div>
                    <div class="swipe-indicator swipe-indicator-right">
                        <div class="indicator-content">
                            <i class="fas fa-arrow-right"></i>
                            <span class="indicator-text">Swipe Right</span>
                            <span class="indicator-action">Like</span>
                        </div>
                    </div>
                </div>
                
                <div id="swipeable-card-container" class="swipeable-card-container">
                    <div id="user-card" class="user-card" data-user-id="{{ $otherUser->id }}">
                        <!-- Photo Section -->
                        <div class="card-photo-section">
                            @if(count($pictures) == 0)
                                <img class="user-photo" src="{{ $otherUser->info->getPicture() }}" alt="Profile picture">
                            @else
                                <div id="carousel" class="carousel slide" data-ride="carousel" data-interval="false" data-wrap="false">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img class="user-photo" src="{{ $otherUser->info->getPicture() }}" alt="Profile picture">
                                        </div>
                                        @foreach($pictures as $picture)
                                            <div class="carousel-item">
                                                <img class="user-photo" src="{{ $picture->getPicture() }}" alt="Picture">
                                            </div>
                                        @endforeach
                                    </div>
                                    <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Info Section -->
                        <div class="card-info-section">
                            <div class="user-header">
                                <h2 class="user-name">{{ $otherUser->info->name . ' ' . $otherUser->info->surname . ', ' . $otherUser->info->age }}</h2>
                                <div class="compatibility-badge" id="compatibility-badge">
                                    <i class="fas fa-heart"></i> 
                                    <span id="compatibility-score">--</span>% Match
                                </div>
                            </div>
                            
                            <div class="user-details-grid">
                                <div class="detail-item">
                                    <i class="fas fa-globe"></i>
                                    <span>{{ $otherUser->info->country }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-language"></i>
                                    <span>{{ $otherUser->info->languages }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-heart"></i>
                                    <span>{{ $otherUser->info->relationship }}</span>
                                </div>
                                @if (
                                    $otherUser->info->tag1 != 'none' &&
                                    $otherUser->info->tag2 != 'none' &&
                                    $otherUser->info->tag3 != 'none'
                                )
                                    <div class="detail-item">
                                        <i class="fas fa-star"></i>
                                        <span>{{ $otherUser->info->tag1 }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-star"></i>
                                        <span>{{ $otherUser->info->tag2 }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-star"></i>
                                        <span>{{ $otherUser->info->tag3 }}</span>
                                    </div>
                                @else
                                    <div class="detail-item full-width">
                                        <i class="fas fa-info-circle"></i>
                                        <span>No interests specified</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="bio-section">
                                <h3><i class="fas fa-quote-left"></i> Bio</h3>
                                <p class="bio-text">{{ $otherUser->info->description }}</p>
                            </div>
                        </div>
                        
                        <!-- Swipe Overlays -->
                        <div class="swipe-overlay like-overlay">
                            <i class="fas fa-heart"></i> LIKE
                        </div>
                        <div class="swipe-overlay dislike-overlay">
                            <i class="fas fa-times"></i> NOPE
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="action-btn dislike-btn" id="dislike-btn" title="Dislike">
                        <i class="fas fa-times"></i>
                    </button>
                    <button class="action-btn like-btn" id="like-btn" title="Like">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Match Modal -->
    <div class="modal fade" id="matchModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content match-modal-content">
                <div class="modal-body text-center">
                    <div class="match-animation">
                        <div class="match-hearts">
                            <i class="fas fa-heart heart-1"></i>
                            <i class="fas fa-heart heart-2"></i>
                            <i class="fas fa-heart heart-3"></i>
                        </div>
                        <h1 class="match-title">It's a Match!</h1>
                        <p class="match-subtitle">You and <span id="match-user-name"></span> liked each other</p>
                        <div class="match-profiles">
                            <img src="{{ $user->info->getPicture() }}" alt="You" class="match-profile-img">
                            <img id="match-user-img" src="" alt="Match" class="match-profile-img">
                        </div>
                        <div class="match-actions">
                            <button class="btn btn-primary btn-lg" onclick="window.location.href='{{ route('chat.index') }}'">
                                <i class="fas fa-comments"></i> Send Message
                            </button>
                            <button class="btn btn-secondary btn-lg" data-dismiss="modal">
                                Keep Swiping
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .home-container {
        min-height: calc(100vh - 76px);
        padding: 30px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 100%;
    }

    .empty-icon {
        font-size: 5rem;
        color: #667eea;
        margin-bottom: 20px;
    }

    .empty-state h1 {
        font-size: 2rem;
        color: #333;
        margin-bottom: 15px;
    }

    .empty-state p {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 30px;
    }

    .swipe-container {
        width: 100%;
        max-width: 450px;
        margin: 0 auto;
        position: relative;
    }

    /* Swipe Direction Indicators */
    .swipe-indicators {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        z-index: 1;
    }

    .swipe-indicator {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.7;
        transition: opacity 0.3s ease;
        z-index: 1;
    }

    .swipe-indicator:hover {
        opacity: 1;
    }

    .swipe-indicator-left {
        left: -140px;
        top: 50%;
        transform: translateY(-50%);
        width: 120px;
        height: 220px;
        background: linear-gradient(90deg, rgba(244, 67, 54, 0.2) 0%, rgba(244, 67, 54, 0.05) 100%);
        border-left: 5px solid #f44336;
        border-radius: 20px 0 0 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 4px 0 15px rgba(244, 67, 54, 0.2);
    }

    .swipe-indicator-right {
        right: -140px;
        top: 50%;
        transform: translateY(-50%);
        width: 120px;
        height: 220px;
        background: linear-gradient(270deg, rgba(76, 175, 80, 0.2) 0%, rgba(76, 175, 80, 0.05) 100%);
        border-right: 5px solid #4CAF50;
        border-radius: 0 20px 20px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: -4px 0 15px rgba(76, 175, 80, 0.2);
    }

    .indicator-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        text-align: center;
    }

    .swipe-indicator-left .indicator-content {
        color: #f44336;
    }

    .swipe-indicator-right .indicator-content {
        color: #4CAF50;
    }

    .swipe-indicator i {
        font-size: 2.5rem;
        margin-bottom: 6px;
        font-weight: bold;
    }

    .swipe-indicator-right i {
        animation: pulse-arrow 2s ease-in-out infinite;
    }

    @keyframes pulse-arrow {
        0%, 100% {
            transform: translateX(0);
            opacity: 0.8;
        }
        50% {
            transform: translateX(5px);
            opacity: 1;
        }
    }

    .swipe-indicator-left i {
        animation: pulse-arrow-left 2s ease-in-out infinite;
    }

    @keyframes pulse-arrow-left {
        0%, 100% {
            transform: translateX(0);
            opacity: 0.8;
        }
        50% {
            transform: translateX(-5px);
            opacity: 1;
        }
    }

    .indicator-text {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .indicator-action {
        font-size: 0.75rem;
        font-weight: 500;
        opacity: 0.8;
    }

    .swipeable-card-container {
        position: relative;
        height: 650px;
        margin-bottom: 25px;
        perspective: 1000px;
        z-index: 2;
    }

    .user-card {
        position: absolute;
        width: 100%;
        height: 100%;
        background: white;
        border-radius: 25px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
        cursor: grab;
        transition: transform 0.3s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .user-card:active {
        cursor: grabbing;
    }

    .user-card.swiping-left {
        transform: rotate(-5deg) translateX(-100px);
    }

    .user-card.swiping-right {
        transform: rotate(5deg) translateX(100px);
    }


    .card-photo-section {
        flex-shrink: 0;
        height: 400px;
        overflow: hidden;
        position: relative;
        touch-action: pan-y pinch-zoom;
        -webkit-user-select: none;
        user-select: none;
    }

    .user-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        pointer-events: none;
        -webkit-user-drag: none;
        user-select: none;
    }

    .carousel {
        touch-action: pan-y pinch-zoom;
    }

    .carousel-inner {
        touch-action: pan-y pinch-zoom;
    }

    .carousel-control-prev,
    .carousel-control-next {
        pointer-events: auto;
        z-index: 15;
    }

    .card-info-section {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .user-header {
        margin-bottom: 20px;
    }

    .user-name {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 12px;
        color: #333;
        line-height: 1.2;
    }

    .compatibility-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .compatibility-badge i {
        font-size: 1.1rem;
    }

    .user-details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 10px;
        font-size: 0.9rem;
        color: #555;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-item i {
        color: #667eea;
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    .bio-section {
        flex: 1;
    }

    .bio-section h3 {
        font-size: 1.2rem;
        color: #333;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bio-section h3 i {
        color: #667eea;
        font-size: 1rem;
    }

    .bio-text {
        font-size: 1rem;
        line-height: 1.7;
        color: #666;
        margin: 0;
    }

    .swipe-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-20deg);
        font-size: 3.5rem;
        font-weight: bold;
        opacity: 0;
        pointer-events: none;
        z-index: 10;
        transition: opacity 0.3s ease;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
    }

    .like-overlay {
        color: #4CAF50;
        border: 5px solid #4CAF50;
        padding: 25px 50px;
        border-radius: 20px;
        background: rgba(76, 175, 80, 0.15);
    }

    .dislike-overlay {
        color: #f44336;
        border: 5px solid #f44336;
        padding: 25px 50px;
        border-radius: 20px;
        background: rgba(244, 67, 54, 0.15);
    }

    .swipe-overlay.visible {
        opacity: 1;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 25px;
        padding: 20px 0;
    }

    .action-btn {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        border: none;
        font-size: 1.4rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        position: relative;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: inherit;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        transform: scale(1.15);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .action-btn:active {
        transform: scale(0.95);
    }

    .dislike-btn {
        background: white;
        color: #f44336;
        border: 3px solid #f44336;
    }

    .dislike-btn:hover {
        background: #f44336;
        color: white;
    }

    .like-btn {
        background: white;
        color: #4CAF50;
        border: 3px solid #4CAF50;
    }

    .like-btn:hover {
        background: #4CAF50;
        color: white;
    }

    /* Match Modal */
    .match-modal-content {
        border: none;
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .match-animation {
        padding: 50px 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .match-hearts {
        position: relative;
        height: 100px;
        margin-bottom: 25px;
    }

    .match-hearts i {
        position: absolute;
        font-size: 3.5rem;
        color: #ffeb3b;
        animation: heartFloat 2s ease-in-out infinite;
    }

    .heart-1 {
        left: 20%;
        animation-delay: 0s;
    }

    .heart-2 {
        left: 50%;
        transform: translateX(-50%);
        animation-delay: 0.3s;
    }

    .heart-3 {
        right: 20%;
        animation-delay: 0.6s;
    }

    @keyframes heartFloat {
        0%, 100% {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        50% {
            transform: translateY(-25px) scale(1.3);
            opacity: 0.9;
        }
    }

    .match-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin: 25px 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .match-subtitle {
        font-size: 1.3rem;
        margin-bottom: 35px;
        opacity: 0.95;
    }

    .match-profiles {
        display: flex;
        justify-content: center;
        gap: 25px;
        margin: 35px 0;
    }

    .match-profile-img {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        border: 6px solid white;
        object-fit: cover;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .match-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 35px;
    }

    .match-actions .btn {
        padding: 14px 35px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1.1rem;
        border: 2px solid white;
        transition: all 0.3s ease;
    }

    .match-actions .btn-primary {
        background: white;
        color: #667eea;
    }

    .match-actions .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .match-actions .btn-secondary {
        background: transparent;
        color: white;
    }

    .match-actions .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Carousel Controls */
    .carousel-control-prev,
    .carousel-control-next {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .carousel-control-prev {
        left: 15px;
    }

    .carousel-control-next {
        right: 15px;
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.95);
    }

    /* Dark Mode Support */
    .dark-mode .home-container {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    }

    .dark-mode .user-card,
    .dark-mode .empty-state {
        background: var(--card-bg);
        color: var(--text-color);
    }

    .dark-mode .user-name,
    .dark-mode .bio-section h3 {
        color: var(--text-color);
    }

    .dark-mode .detail-item {
        background: #2d2d2d;
        color: var(--text-color);
    }

    .dark-mode .bio-text {
        color: #ccc;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .home-container {
            padding: 20px 15px;
        }

        .swipe-indicator-left {
            left: -80px;
            width: 70px;
            height: 150px;
        }

        .swipe-indicator-right {
            right: -80px;
            width: 70px;
            height: 150px;
        }

        .swipe-indicator i {
            font-size: 1.5rem;
        }

        .indicator-text {
            font-size: 0.7rem;
        }

        .indicator-action {
            font-size: 0.65rem;
        }

        .swipeable-card-container {
            height: 580px;
        }

        .card-photo-section {
            height: 350px;
        }

        .action-btn {
            width: 60px;
            height: 60px;
            font-size: 1.2rem;
        }

        .user-name {
            font-size: 1.5rem;
        }

        .user-details-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .swipe-indicator-left,
        .swipe-indicator-right {
            display: none;
        }

        .swipeable-card-container {
            height: 550px;
        }

        .card-photo-section {
            height: 300px;
        }

        .action-buttons {
            gap: 15px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const card = document.getElementById('user-card');
    if (!card) return;
    
    const userId = card.dataset.userId;
    let startX = 0, startY = 0, currentX = 0, currentY = 0;
    let isDragging = false;
    let isSwipeGesture = false;
    let swipeThreshold = 100;
    let carousel = document.getElementById('carousel');
    let carouselInstance = null;
    
    // Disable Bootstrap carousel touch gestures if it exists
    if (carousel && typeof $ !== 'undefined' && $.fn.carousel) {
        carouselInstance = $(carousel).carousel({
            interval: false,
            wrap: false,
            touch: false  // Disable touch gestures on carousel
        });
        
        // Prevent carousel from responding to touch events
        const carouselInner = carousel.querySelector('.carousel-inner');
        if (carouselInner) {
            carouselInner.addEventListener('touchstart', function(e) {
                // Allow carousel controls to work
                if (e.target.closest('.carousel-control-prev') || e.target.closest('.carousel-control-next')) {
                    return;
                }
                // For images, let the swipe handler take over
                e.stopPropagation();
            }, { passive: true });
        }
    }

    // Load compatibility score
    fetch(`/profile/compatibility/${userId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('compatibility-score').textContent = data.compatibility;
        })
        .catch(error => console.error('Error loading compatibility:', error));

    // Touch events
    card.addEventListener('touchstart', handleStart);
    card.addEventListener('touchmove', handleMove);
    card.addEventListener('touchend', handleEnd);

    // Mouse events
    card.addEventListener('mousedown', handleStart);
    document.addEventListener('mousemove', handleMove);
    document.addEventListener('mouseup', handleEnd);

    function handleStart(e) {
        // Check if clicking on carousel controls - if so, don't start swipe
        const target = e.target;
        if (target.closest('.carousel-control-prev') || target.closest('.carousel-control-next')) {
            return;
        }
        
        isDragging = true;
        isSwipeGesture = false;
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        startX = clientX;
        startY = clientY;
        card.style.transition = 'none';
        
        // Disable carousel during potential swipe
        if (carouselInstance) {
            carouselInstance.carousel('pause');
        }
    }

    function handleMove(e) {
        if (!isDragging) return;
        
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        
        currentX = clientX - startX;
        currentY = clientY - startY;
        
        // Determine if this is a swipe gesture (horizontal movement > vertical)
        const absX = Math.abs(currentX);
        const absY = Math.abs(currentY);
        
        // If horizontal movement is significant, treat as swipe
        if (absX > 30 && absX > absY * 1.5) {
            isSwipeGesture = true;
            e.preventDefault();
            e.stopPropagation();
            
            const rotation = currentX * 0.1;
            card.style.transform = `translate(${currentX}px, ${currentY}px) rotate(${rotation}deg)`;
            
            // Show overlay based on swipe direction
            const likeOverlay = document.querySelector('.like-overlay');
            const dislikeOverlay = document.querySelector('.dislike-overlay');
            
            // Hide all overlays first
            likeOverlay.classList.remove('visible');
            dislikeOverlay.classList.remove('visible');
            
            if (absX > 50) {
                if (currentX > 0) {
                    // Swipe right - like
                    likeOverlay.classList.add('visible');
                    card.classList.add('swiping-right');
                    card.classList.remove('swiping-left');
                } else {
                    // Swipe left - dislike
                    dislikeOverlay.classList.add('visible');
                    card.classList.add('swiping-left');
                    card.classList.remove('swiping-right');
                }
            }
        } else if (!isSwipeGesture && absY > 10) {
            // Small vertical movement - allow carousel to work
            // Don't prevent default to allow carousel scrolling
        }
    }

    function handleEnd(e) {
        if (!isDragging) return;
        isDragging = false;
        card.style.transition = 'transform 0.3s ease';
        
        const absX = Math.abs(currentX);
        
        // Hide overlays
        document.querySelectorAll('.swipe-overlay').forEach(overlay => {
            overlay.classList.remove('visible');
        });
        
        // Only process swipe if it was a swipe gesture
        if (isSwipeGesture && absX > swipeThreshold) {
            if (currentX > 0) {
                // Swipe right - Like
                performAction('like');
            } else if (currentX < 0) {
                // Swipe left - Dislike
                performAction('dislike');
            }
        } else {
            // Snap back
            card.style.transform = 'translate(0, 0) rotate(0deg)';
            card.classList.remove('swiping-left', 'swiping-right');
            
            // Re-enable carousel if it wasn't a swipe
            if (carouselInstance && !isSwipeGesture) {
                carouselInstance.carousel('cycle');
            }
        }
        
        currentX = 0;
        currentY = 0;
        isSwipeGesture = false;
    }

    function performAction(action) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        // Animate card out
        const direction = action === 'like' ? 'right' : 'left';
        card.style.transform = `translateX(${direction === 'right' ? '1000' : '-1000'}px) rotate(${direction === 'right' ? '30' : '-30'}deg)`;
        card.style.opacity = '0';
        
        // Send request
        fetch(`/profile/${action}/${userId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Like response:', data);
            if (data.matched) {
                // Show match modal
                document.getElementById('match-user-name').textContent = data.match_user.name;
                document.getElementById('match-user-img').src = data.match_user.picture;
                $('#matchModal').modal('show');
                // Reload after modal is closed to refresh matches
                $('#matchModal').on('hidden.bs.modal', function () {
                    window.location.reload();
                });
            } else {
                // Reload page for next user (force reload to clear cache)
                setTimeout(() => {
                    window.location.href = window.location.href;
                }, 300);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.location.reload();
        });
    }

    // Button click handlers
    document.getElementById('like-btn').addEventListener('click', () => performAction('like'));
    document.getElementById('dislike-btn').addEventListener('click', () => performAction('dislike'));
});
</script>
