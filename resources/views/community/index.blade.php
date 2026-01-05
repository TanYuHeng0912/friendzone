@extends('layouts.app')

@section('content')
<div class="communities-page">
    <!-- Page Header -->
    <div class="communities-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-users"></i> Interest Communities
            </h1>
            <p class="page-subtitle">Join communities based on your interests and connect with like-minded people!</p>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert-container">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-container">
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Communities Grid -->
    @if($communities->count() > 0)
        <div class="communities-grid">
            @foreach($communities as $community)
                <div class="community-card {{ in_array($community->id, $userCommunities) ? 'joined' : '' }}">
                    <div class="card-header">
                        <div class="icon-wrapper">
                            <div class="community-icon">{{ $community->icon }}</div>
                        </div>
                        <div class="badge-wrapper">
                            @if(in_array($community->id, $userCommunities))
                                <span class="member-badge">
                                    <i class="fas fa-check"></i> Member
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <h3 class="community-name">{{ $community->name }}</h3>
                        <p class="community-description">{{ $community->description }}</p>
                        
                        <div class="community-stats">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-value">{{ $community->members_count }}</span>
                                    <span class="stat-label">Members</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-value">{{ $community->posts_count }}</span>
                                    <span class="stat-label">Posts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        @if(in_array($community->id, $userCommunities))
                            <a href="{{ route('community.show', $community) }}" class="btn-enter">
                                <i class="fas fa-comments"></i>
                                <span>Enter Community</span>
                            </a>
                            <form action="{{ route('community.leave', $community) }}" method="POST" class="leave-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-leave" 
                                        onclick="return confirm('Are you sure you want to leave this community?')">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Leave</span>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('community.join', $community) }}" method="POST" class="join-form">
                                @csrf
                                <button type="submit" class="btn-join">
                                    <i class="fas fa-plus"></i>
                                    <span>Join Community</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-users"></i>
            </div>
            <h2>No Communities Yet</h2>
            <p>Communities will appear here once they are created.</p>
        </div>
    @endif
</div>

<style>
.communities-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 30px 20px;
    min-height: calc(100vh - 200px);
}

.communities-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 2px solid #e9ecef;
}

.header-content {
    max-width: 800px;
    margin: 0 auto;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 12px;
}

.page-title i {
    color: #667eea;
    -webkit-text-fill-color: #667eea;
    margin-right: 12px;
}

.page-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
    margin: 0;
}

.alert-container {
    max-width: 1200px;
    margin: 0 auto 30px;
}

.alert {
    padding: 16px 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    position: relative;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert i {
    font-size: 1.2rem;
}

.alert-close {
    margin-left: auto;
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.alert-close:hover {
    opacity: 1;
}

.communities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
    margin-top: 30px;
}

.community-card {
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    display: flex;
    flex-direction: column;
    position: relative;
}

.community-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
}

.community-card.joined {
    border-color: #28a745;
}

.community-card.joined::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #28a745, #20c997);
}

.card-header {
    position: relative;
    padding: 30px 24px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    text-align: center;
}

.icon-wrapper {
    margin-bottom: 16px;
}

.community-icon {
    font-size: 4rem;
    display: inline-block;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
    transition: transform 0.3s ease;
}

.community-card:hover .community-icon {
    transform: scale(1.1) rotate(5deg);
}

.badge-wrapper {
    position: absolute;
    top: 16px;
    right: 16px;
}

.member-badge {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.member-badge i {
    font-size: 0.7rem;
}

.card-body {
    padding: 0 24px 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.community-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 12px 0;
    text-align: center;
}

.community-description {
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0 0 20px 0;
    text-align: center;
    flex: 1;
}

.community-stats {
    display: flex;
    justify-content: space-around;
    gap: 16px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
    margin-top: auto;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-footer {
    padding: 20px 24px;
    border-top: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn-enter,
.btn-join,
.btn-leave {
    width: 100%;
    padding: 12px 20px;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-enter {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-enter:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    text-decoration: none;
    color: white;
}

.btn-join {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-join:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.btn-leave {
    background: transparent;
    color: #dc3545;
    border: 2px solid #dc3545;
    padding: 8px 16px;
    font-size: 0.85rem;
}

.btn-leave:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-1px);
}

.join-form,
.leave-form {
    margin: 0;
    width: 100%;
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

/* Responsive Design */
@media (max-width: 768px) {
    .communities-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .community-card {
        border-radius: 16px;
    }
}

@media (max-width: 480px) {
    .communities-page {
        padding: 20px 15px;
    }
    
    .page-title {
        font-size: 1.75rem;
    }
    
    .community-stats {
        flex-direction: column;
        gap: 12px;
    }
    
    .stat-item {
        justify-content: center;
    }
}
</style>
@endsection