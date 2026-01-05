@extends('layouts.adminapp')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your application')

@section('content')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        margin: 20px 0;
    }
    
    .recent-item {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 10px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .recent-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    
    .quick-action-card {
        text-align: center;
        padding: 25px;
        border-radius: 15px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .quick-action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        color: inherit;
        text-decoration: none;
    }
    
    .quick-action-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.5rem;
        color: white;
    }
</style>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card primary">
            <div class="stat-card-header">
                <div class="stat-card-title">Total Users</div>
                <div class="stat-card-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-card-value">{{ number_format($stats['users_count']) }}</div>
            <div class="stat-card-footer">
                <i class="fas fa-arrow-up text-success"></i> +{{ $stats['new_users_this_week'] }} this week
            </div>
            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-gradient mt-3 w-100">
                <i class="fas fa-eye"></i> View All
            </a>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card success">
            <div class="stat-card-header">
                <div class="stat-card-title">Communities</div>
                <div class="stat-card-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
            </div>
            <div class="stat-card-value">{{ number_format($stats['communities_count']) }}</div>
            <div class="stat-card-footer">
                <i class="fas fa-file-alt"></i> {{ $stats['posts_count'] }} total posts
            </div>
            <a href="{{ route('admin.communities') }}" class="btn btn-sm btn-gradient mt-3 w-100">
                <i class="fas fa-cog"></i> Manage
            </a>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card warning">
            <div class="stat-card-header">
                <div class="stat-card-title">Feedback</div>
                <div class="stat-card-icon">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
            <div class="stat-card-value">{{ number_format($stats['feedback_count']) }}</div>
            <div class="stat-card-footer">
                <i class="fas fa-bell"></i> User feedback & reports
            </div>
            <a href="{{ route('admin.feedback') }}" class="btn btn-sm btn-gradient mt-3 w-100">
                <i class="fas fa-eye"></i> View All
            </a>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card info">
            <div class="stat-card-header">
                <div class="stat-card-title">Active Today</div>
                <div class="stat-card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-card-value">{{ number_format($stats['active_users_today']) }}</div>
            <div class="stat-card-footer">
                <i class="fas fa-circle text-success"></i> {{ $stats['online_users'] }} online now
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card purple">
            <div class="stat-card-header">
                <div class="stat-card-title">Matches</div>
                <div class="stat-card-icon">
                    <i class="fas fa-heart"></i>
                </div>
            </div>
            <div class="stat-card-value">{{ number_format($stats['matches_count']) }}</div>
            <div class="stat-card-footer">
                <i class="fas fa-percentage"></i> {{ number_format($engagement['match_rate'], 1) }}% match rate
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card danger">
            <div class="stat-card-header">
                <div class="stat-card-title">Messages</div>
                <div class="stat-card-icon">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
            <div class="stat-card-value">{{ number_format($stats['messages_count']) }}</div>
            <div class="stat-card-footer">
                <i class="fas fa-comments"></i> {{ $stats['chats_count'] }} active chats
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card success">
            <div class="stat-card-header">
                <div class="stat-card-title">Friendships</div>
                <div class="stat-card-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
            </div>
            <div class="stat-card-value">{{ number_format($stats['friendships_count']) }}</div>
            <div class="stat-card-footer">
                <i class="fas fa-check-circle"></i> Accepted connections
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card info">
            <div class="stat-card-header">
                <div class="stat-card-title">New This Month</div>
                <div class="stat-card-icon">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
            <div class="stat-card-value">{{ number_format($stats['new_users_this_month']) }}</div>
            <div class="stat-card-footer">
                <i class="fas fa-user-plus"></i> New registrations
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-md-8 mb-3">
        <div class="stat-card">
            <h5 class="mb-3"><i class="fas fa-chart-area"></i> User Growth (Last 30 Days)</h5>
            <div class="chart-container">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <h5 class="mb-3"><i class="fas fa-chart-pie"></i> Activity Breakdown</h5>
            <div class="chart-container">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <h5 class="mb-3"><i class="fas fa-user-plus"></i> Recent Users</h5>
            @forelse($recent_users as $user)
                <div class="recent-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $user->info->first_name ?? 'N/A' }} {{ $user->info->last_name ?? '' }}</strong>
                            <br>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-3">No recent users</p>
            @endforelse
            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-gradient w-100 mt-2">
                <i class="fas fa-arrow-right"></i> View All Users
            </a>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <h5 class="mb-3"><i class="fas fa-users-cog"></i> Recent Communities</h5>
            @forelse($recent_communities as $community)
                <div class="recent-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $community->name }}</strong>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-users"></i> {{ $community->members_count }} members
                                <i class="fas fa-file-alt ml-2"></i> {{ $community->posts_count }} posts
                            </small>
                        </div>
                        <small class="text-muted">{{ $community->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-3">No recent communities</p>
            @endforelse
            <a href="{{ route('admin.communities') }}" class="btn btn-sm btn-gradient w-100 mt-2">
                <i class="fas fa-arrow-right"></i> View All Communities
            </a>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <h5 class="mb-3"><i class="fas fa-comments"></i> Recent Feedback</h5>
            @forelse($recent_feedback as $feedback)
                <div class="recent-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ Str::limit($feedback->subject, 30) }}</strong>
                            <br>
                            <small class="text-muted">by {{ $feedback->user->info->first_name ?? 'Anonymous' }}</small>
                        </div>
                        <div class="text-right">
                            @if(!$feedback->is_read)
                                <span class="badge bg-warning text-dark">New</span><br>
                            @endif
                            <small class="text-muted">{{ $feedback->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-3">No recent feedback</p>
            @endforelse
            <a href="{{ route('admin.feedback') }}" class="btn btn-sm btn-gradient w-100 mt-2">
                <i class="fas fa-arrow-right"></i> View All Feedback
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-12">
        <div class="stat-card">
            <h5 class="mb-4"><i class="fas fa-bolt"></i> Quick Actions</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="{{ route('admin.communities.create') }}" class="quick-action-card">
                        <div class="quick-action-icon" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h6>Create Community</h6>
                        <small class="text-muted">Add a new community</small>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('admin.users') }}" class="quick-action-card">
                        <div class="quick-action-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6>Manage Users</h6>
                        <small class="text-muted">View and manage users</small>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('admin.feedback') }}" class="quick-action-card">
                        <div class="quick-action-icon" style="background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h6>View Feedback</h6>
                        <small class="text-muted">Review user feedback</small>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('admin.communities') }}" class="quick-action-card">
                        <div class="quick-action-icon" style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h6>Communities</h6>
                        <small class="text-muted">Manage communities</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userGrowthCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($userGrowth, 'date')) !!},
        datasets: [{
            label: 'New Users',
            data: {!! json_encode(array_column($userGrowth, 'count')) !!},
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Activity Breakdown Chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
new Chart(activityCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($activityBreakdown->pluck('type')->toArray()) !!},
        datasets: [{
            data: {!! json_encode($activityBreakdown->pluck('count')->toArray()) !!},
            backgroundColor: [
                'rgba(102, 126, 234, 0.8)',
                'rgba(72, 187, 120, 0.8)',
                'rgba(237, 137, 54, 0.8)',
                'rgba(66, 153, 225, 0.8)',
                'rgba(245, 101, 101, 0.8)',
                'rgba(159, 122, 234, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection
