@extends('layouts.adminapp')

@section('page-title', 'User Management')
@section('page-subtitle', 'Manage and monitor all users')

@section('content')
<style>
    .search-filter-bar {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
    }
    
    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    .btn-action {
        padding: 5px 12px;
        font-size: 0.85rem;
        border-radius: 5px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-users"></i> User Management</h2>
        <p class="text-muted mb-0">Total: {{ $users->total() }} users</p>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="search-filter-bar">
    <form method="GET" action="{{ route('admin.users') }}" class="row g-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by name or email..." 
                       value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                <option value="admin" {{ request('status') == 'admin' ? 'selected' : '' }}>Admins</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-gradient w-100">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.users') }}" class="btn btn-secondary w-100">
                <i class="fas fa-redo"></i> Reset
            </a>
        </div>
    </form>
</div>

<div class="stat-card">
    <div class="table-responsive">
        <table class="admin-table table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Joined</th>
                    <th>Posts</th>
                    <th>Communities</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($user->info && $user->info->profile_picture)
                                    <img src="{{ asset('storage/' . $user->info->profile_picture) }}" 
                                         alt="Profile" class="user-avatar">
                                @else
                                    <div class="user-avatar bg-secondary d-flex align-items-center justify-content-center text-white">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ $user->info->first_name ?? 'N/A' }} {{ $user->info->last_name ?? '' }}</strong>
                                    @if($user->is_admin)
                                        <br><span class="badge bg-primary">Admin</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            {{ $user->created_at->format('M d, Y') }}
                            <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                        <td><span class="badge bg-info">{{ $user->posts_count }}</span></td>
                        <td><span class="badge bg-info">{{ $user->communities_count }}</span></td>
                        <td>
                            @if($user->is_banned)
                                <span class="status-badge bg-danger text-white">Banned</span>
                            @elseif($user->is_suspended && $user->suspended_until && $user->suspended_until > now())
                                <span class="status-badge bg-warning text-dark">Suspended</span>
                                <br><small class="text-muted">Until: {{ $user->suspended_until->format('M d, Y') }}</small>
                            @else
                                <span class="status-badge bg-success text-white">Active</span>
                            @endif
                        </td>
                        <td>
                            @if(!$user->is_admin)
                                <div class="action-buttons">
                                    @if($user->is_banned)
                                        <form action="{{ route('admin.users.unban', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success btn-action" 
                                                    onclick="return confirm('Unban this user?')">
                                                <i class="fas fa-unlock"></i> Unban
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-warning btn-action" 
                                                data-toggle="modal" data-target="#suspendModal{{ $user->id }}">
                                            <i class="fas fa-clock"></i> Suspend
                                        </button>
                                        
                                        <form action="{{ route('admin.users.ban', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger btn-action" 
                                                    onclick="return confirm('Ban this user?')">
                                                <i class="fas fa-ban"></i> Ban
                                            </button>
                                        </form>
                                    @endif

                                    @if($user->is_suspended && $user->suspended_until && $user->suspended_until > now())
                                        <form action="{{ route('admin.users.unsuspend', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-info btn-action">
                                                <i class="fas fa-play"></i> Unsuspend
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Suspend Modal -->
                                <div class="modal fade" id="suspendModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.users.suspend', $user) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Suspend User</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Suspend <strong>{{ $user->info->first_name ?? 'User' }}</strong> for how many days?</p>
                                                    <div class="mb-3">
                                                        <label for="suspension_days" class="form-label">Days</label>
                                                        <input type="number" class="form-control" 
                                                               name="suspension_days" min="1" max="365" value="7" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-warning">Suspend User</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">Admin User</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No users found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
