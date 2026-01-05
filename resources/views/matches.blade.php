@extends('layouts.app')

@section('content')
    <div class="matches-container">
        @if(count($users) == 0)
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h2>No Matches Yet</h2>
                <p>Start swiping to find your perfect match!</p>
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-search"></i> Discover People
                </a>
            </div>
        @else
            <div class="matches-header">
                <h1 class="matches-title">
                    <i class="fas fa-heart"></i> Your Matches
                </h1>
                <p class="matches-subtitle">{{ count($users) }} {{ count($users) == 1 ? 'match' : 'matches' }} found</p>
            </div>
            
            <div class="matches-grid">
                @foreach($users as $otherUser)
                    <div class="match-card">
                        <div class="match-card-inner">
                            <!-- Profile Picture Section -->
                            <div class="profile-image-container">
                                <div class="profile-image-wrapper">
                                    <img src="{{ $otherUser->info->getPicture() }}"
                                         alt="{{ $otherUser->info->name }}"
                                         class="profile-image">
                                    <div class="profile-overlay"></div>
                                </div>
                                @if($otherUser->isOnline())
                                    <span class="online-badge" title="Online">
                                        <i class="fas fa-circle"></i> Online
                                    </span>
                                @endif
                            </div>

                            <!-- Profile Info Section -->
                            <div class="profile-info-section">
                                <div class="profile-header">
                                    <h2 class="profile-name">
                                        {{ $otherUser->info->name }} {{ $otherUser->info->surname }}
                                        <span class="profile-age">{{ $otherUser->info->age }}</span>
                                    </h2>
                                </div>

                                <!-- Quick Info Tags -->
                                <div class="info-tags">
                                    @if($otherUser->info->country)
                                        <span class="info-tag">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ $otherUser->info->country }}
                                        </span>
                                    @endif
                                    @if($otherUser->info->relationship)
                                        <span class="info-tag">
                                            <i class="fas fa-heart"></i>
                                            {{ $otherUser->info->relationship }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Details Grid -->
                                <div class="details-grid">
                                    @if($otherUser->info->languages)
                                        <div class="detail-item">
                                            <i class="fas fa-language"></i>
                                            <div class="detail-content">
                                                <span class="detail-label">Languages</span>
                                                <span class="detail-value">{{ $otherUser->info->languages }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($otherUser->info->tag1)
                                        <div class="detail-item">
                                            <i class="fas fa-star"></i>
                                            <div class="detail-content">
                                                <span class="detail-label">Interest</span>
                                                <span class="detail-value">{{ $otherUser->info->tag1 }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($otherUser->info->tag2 && $otherUser->info->tag2 !== 'none')
                                        <div class="detail-item">
                                            <i class="fas fa-star"></i>
                                            <div class="detail-content">
                                                <span class="detail-label">Interest</span>
                                                <span class="detail-value">{{ $otherUser->info->tag2 }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($otherUser->info->tag3 && $otherUser->info->tag3 !== 'none')
                                        <div class="detail-item">
                                            <i class="fas fa-star"></i>
                                            <div class="detail-content">
                                                <span class="detail-label">Interest</span>
                                                <span class="detail-value">{{ $otherUser->info->tag3 }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Bio Section -->
                                @if($otherUser->info->description)
                                    <div class="bio-section">
                                        <h4 class="bio-title">
                                            <i class="fas fa-quote-left"></i> About
                                        </h4>
                                        <p class="bio-text">{{ $otherUser->info->description }}</p>
                                    </div>
                                @endif

                                <!-- Contact Info -->
                                <div class="contact-info">
                                    <div class="contact-item">
                                        <i class="fas fa-envelope"></i>
                                        <span>{{ $otherUser->email }}</span>
                                    </div>
                                    @if($otherUser->info->phone)
                                        <div class="contact-item">
                                            <i class="fas fa-phone"></i>
                                            <span>{{ $otherUser->info->phone }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Button -->
                                <div class="action-section">
                                    <form action="{{ route('chat.create', $otherUser->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-start-chat">
                                            <i class="fas fa-comments"></i>
                                            <span>Start Chat</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

<style>
.matches-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
    min-height: calc(100vh - 200px);
}

.matches-header {
    text-align: center;
    margin-bottom: 40px;
}

.matches-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 10px;
}

.matches-title i {
    color: #667eea;
    -webkit-text-fill-color: #667eea;
    margin-right: 10px;
}

.matches-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
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
}

.empty-state p {
    color: #6c757d;
    font-size: 1.1rem;
}

.matches-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.match-card {
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
}

.match-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
}

.match-card-inner {
    display: flex;
    flex-direction: column;
}

.profile-image-container {
    position: relative;
    width: 100%;
    height: 300px;
    overflow: hidden;
}

.profile-image-wrapper {
    width: 100%;
    height: 100%;
    position: relative;
}

.profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.match-card:hover .profile-image {
    transform: scale(1.05);
}

.profile-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100px;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
}

.online-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #28a745;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
}

.online-badge i {
    font-size: 0.7rem;
}

.profile-info-section {
    padding: 25px;
}

.profile-header {
    margin-bottom: 15px;
}

.profile-name {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.profile-age {
    font-size: 1.2rem;
    color: #6c757d;
    font-weight: 400;
}

.info-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
}

.info-tag {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.info-tag i {
    font-size: 0.75rem;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.detail-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 12px;
    transition: all 0.2s ease;
}

.detail-item:hover {
    background: #e9ecef;
    transform: translateX(3px);
}

.detail-item i {
    color: #667eea;
    font-size: 1.1rem;
    margin-top: 2px;
}

.detail-content {
    display: flex;
    flex-direction: column;
}

.detail-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
}

.detail-value {
    font-size: 0.95rem;
    color: #2c3e50;
    font-weight: 600;
}

.bio-section {
    margin: 20px 0;
    padding: 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    border-left: 4px solid #667eea;
}

.bio-title {
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.bio-title i {
    color: #667eea;
}

.bio-text {
    color: #495057;
    line-height: 1.6;
    margin: 0;
    font-size: 0.95rem;
}

.contact-info {
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    color: #495057;
    font-size: 0.9rem;
}

.contact-item i {
    color: #667eea;
    width: 20px;
}

.action-section {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 2px solid #e9ecef;
}

.btn-start-chat {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 14px 24px;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-start-chat:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.btn-start-chat:active {
    transform: translateY(0);
}

.btn-start-chat i {
    font-size: 1.1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .matches-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .matches-title {
        font-size: 2rem;
    }
    
    .profile-image-container {
        height: 250px;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .matches-container {
        padding: 15px 10px;
    }
    
    .profile-info-section {
        padding: 20px;
    }
    
    .profile-name {
        font-size: 1.5rem;
    }
}
</style>
@endsection