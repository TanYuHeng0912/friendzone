@extends('layouts.adminapp')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-users"></i> User Management</h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Users ({{ $users->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
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
                                        <td>{{ $user->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($user->info && $user->info->profile_picture)
                                                    <img src="{{ asset('storage/' . $user->info->profile_picture) }}" 
                                                         alt="Profile" class="rounded-circle me-2" width="40" height="40">
                                                @else
                                                    <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $user->info->first_name ?? 'N/A' }} {{ $user->info->last_name ?? '' }}</strong>
                                                    @if($user->is_admin)
                                                        <span class="badge badge-primary">Admin</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        <td>{{ $user->posts_count }}</td>
                                        <td>{{ $user->communities_count }}</td>
                                        <td>
                                            @if($user->is_banned)
                                                <span class="badge badge-danger">Banned</span>
                                            @elseif($user->is_suspended && $user->suspended_until > now())
                                                <span class="badge badge-warning">Suspended</span>
                                                <br><small>Until: {{ $user->suspended_until->format('M d, Y') }}</small>
                                            @else
                                                <span class="badge badge-success">Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$user->is_admin)
                                                <div class="btn-group" role="group">
                                                    @if($user->is_banned)
                                                        <form action="{{ route('admin.users.unban', $user) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-success" 
                                                                    onclick="confirm('Are you sure you want to unban this user?')">
                                                                <i class="fas fa-unlock"></i> Unban
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-warning" 
                                                                data-toggle="modal" data-target="#suspendModal{{ $user->id }}">
                                                            <i class="fas fa-clock"></i> Suspend
                                                        </button>
                                                        
                                                        <form action="{{ route('admin.users.ban', $user) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('Are you sure you want to ban this user?')">
                                                                <i class="fas fa-ban"></i> Ban
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($user->is_suspended && $user->suspended_until > now())
                                                        <form action="{{ route('admin.users.unsuspend', $user) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-info">
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
                                                                    <div class="form-group">
                                                                        <label for="suspension_days">Days</label>
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
                                        <td colspan="8" class="text-center">No users found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection