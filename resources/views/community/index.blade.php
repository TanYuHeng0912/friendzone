@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">Interest Communities</h1>
            <p class="text-center text-muted mb-5">Join communities based on your interests and connect with like-minded people!</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        @foreach($communities as $community)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="community-card">
                    <div class="community-header">
                        <div class="community-icon">{{ $community->icon }}</div>
                        <h4 class="community-name">{{ $community->name }}</h4>
                    </div>
                    
                    <div class="community-body">
                        <p class="community-description">{{ $community->description }}</p>
                        
                        <div class="community-stats">
                            <span class="stat">
                                <i class="fas fa-users"></i>
                                {{ $community->members_count }} members
                            </span>
                            <span class="stat">
                                <i class="fas fa-newspaper"></i>
                                {{ $community->posts_count }} posts
                            </span>
                        </div>
                    </div>
                    
                    <div class="community-footer">
                        @if(in_array($community->id, $userCommunities))
                            <a href="{{ route('community.show', $community) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-comments"></i> Enter Community
                            </a>
                            <form action="{{ route('community.leave', $community) }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm btn-block" 
                                        onclick="return confirm('Are you sure you want to leave this community?')">
                                    Leave Community
                                </button>
                            </form>
                        @else
                            <form action="{{ route('community.join', $community) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-plus"></i> Join Community
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
.community-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    padding: 20px;
    height: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e9ecef;
}

.community-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.community-header {
    text-align: center;
    margin-bottom: 15px;
}

.community-icon {
    font-size: 3rem;
    margin-bottom: 10px;
}

.community-name {
    color: #333;
    font-weight: 600;
    margin-bottom: 0;
}

.community-body {
    margin-bottom: 20px;
}

.community-description {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 15px;
}

.community-stats {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #888;
}

.stat {
    display: flex;
    align-items: center;
    gap: 5px;
}

.community-footer {
    margin-top: auto;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
}

.btn-success {
    background: linear-gradient(45deg, #28a745, #1e7e34);
    border: none;
}

.btn:hover {
    transform: translateY(-1px);
}

.alert {
    border-radius: 10px;
    border: none;
}
</style>
@endsection