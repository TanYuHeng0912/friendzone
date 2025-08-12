@extends('layouts.adminapp')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-tachometer-alt"></i> Admin Dashboard
            </h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['users_count'] }}</h4>
                            <p class="mb-0">Total Users</p>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>+{{ $stats['new_users_this_week'] }} this week</small>
                    </div>
                </div>
                <a href="{{ route('admin.users') }}" class="card-footer text-white-50 text-decoration-none">
                    <span class="float-left">View Details</span>
                    <span class="float-right">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['communities_count'] }}</h4>
                            <p class="mb-0">Communities</p>
                        </div>
                        <div>
                            <i class="fas fa-users-cog fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>{{ $stats['posts_count'] }} total posts</small>
                    </div>
                </div>
                <a href="{{ route('admin.communities') }}" class="card-footer text-white-50 text-decoration-none">
                    <span class="float-left">Manage Communities</span>
                    <span class="float-right">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['feedback_count'] }}</h4>
                            <p class="mb-0">Feedback</p>
                        </div>
                        <div>
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>User feedback & reports</small>
                    </div>
                </div>
                <a href="{{ route('admin.feedback') }}" class="card-footer text-white-50 text-decoration-none">
                    <span class="float-left">View Feedback</span>
                    <span class="float-right">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['active_users_today'] }}</h4>
                            <p class="mb-0">Active Today</p>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>Users active today</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user-plus"></i> Recent Users</h5>
                </div>
                <div class="card-body">
                    @forelse($recent_users as $user)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $user->info->first_name ?? 'N/A' }} {{ $user->info->last_name ?? '' }}</strong>
                                <br>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @empty
                        <p class="text-muted">No recent users</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-users-cog"></i> Recent Communities</h5>
                </div>
                <div class="card-body">
                    @forelse($recent_communities as $community)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $community->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $community->members_count }} members, {{ $community->posts_count }} posts</small>
                            </div>
                            <small class="text-muted">{{ $community->created_at->diffForHumans() }}</small>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @empty
                        <p class="text-muted">No recent communities</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-comments"></i> Recent Feedback</h5>
                </div>
                <div class="card-body">
                    @forelse($recent_feedback as $feedback)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $feedback->subject }}</strong>
                                <br>
                                <small class="text-muted">by {{ $feedback->user->info->first_name ?? 'Anonymous' }}</small>
                            </div>
                            <div class="text-right">
                                @if(!$feedback->is_read)
                                    <span class="badge badge-warning">New</span>
                                @endif
                                <br>
                                <small class="text-muted">{{ $feedback->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @empty
                        <p class="text-muted">No recent feedback</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('admin.communities.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-plus"></i> Create Community
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.users') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.feedback') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-comments"></i> View Feedback
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.communities') }}" class="btn btn-info btn-block">
                                <i class="fas fa-cog"></i> Community Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection