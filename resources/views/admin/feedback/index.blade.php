@extends('layouts.adminapp')

@section('page-title', 'Feedback Management')
@section('page-subtitle', 'Review and manage user feedback')

@section('content')
<style>
    .feedback-item {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 10px;
        background: #f8f9fa;
        border-left: 4px solid transparent;
        transition: all 0.3s ease;
    }
    
    .feedback-item.unread {
        background: #fff3cd;
        border-left-color: #ffc107;
    }
    
    .feedback-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-comments"></i> Feedback Management</h2>
        <p class="text-muted mb-0">Total: {{ $feedback->total() }} feedback items</p>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="search-filter-bar">
    <form method="GET" action="{{ route('admin.feedback') }}" class="row g-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control" 
                       placeholder="Search feedback..." 
                       value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-2">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="bug_report" {{ request('type') == 'bug_report' ? 'selected' : '' }}>Bug Report</option>
                <option value="feature_request" {{ request('type') == 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                <option value="complaint" {{ request('type') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                <option value="suggestion" {{ request('type') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="read_status" class="form-select">
                <option value="">All Status</option>
                <option value="unread" {{ request('read_status') == 'unread' ? 'selected' : '' }}>Unread</option>
                <option value="read" {{ request('read_status') == 'read' ? 'selected' : '' }}>Read</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-gradient w-100">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.feedback') }}" class="btn btn-secondary w-100">
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
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedback as $item)
                    <tr class="{{ !$item->is_read ? 'table-warning' : '' }}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($item->user && $item->user->info && $item->user->info->profile_picture)
                                    <img src="{{ asset('storage/' . $item->user->info->profile_picture) }}" 
                                         alt="Profile" class="user-avatar">
                                @else
                                    <div class="user-avatar bg-secondary d-flex align-items-center justify-content-center text-white">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                                <div>
                                    @if($item->user)
                                        <strong>{{ $item->user->info->first_name ?? 'Unknown' }} {{ $item->user->info->last_name ?? '' }}</strong>
                                        <br><small class="text-muted">{{ $item->user->email }}</small>
                                    @else
                                        <span class="text-muted">Deleted User</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>{{ $item->subject }}</strong>
                            @if(!$item->is_read)
                                <br><span class="badge bg-warning text-dark">New</span>
                            @endif
                        </td>
                        <td>
                            @switch($item->type)
                                @case('bug_report')
                                    <span class="badge bg-danger">Bug Report</span>
                                    @break
                                @case('feature_request')
                                    <span class="badge bg-info">Feature Request</span>
                                    @break
                                @case('complaint')
                                    <span class="badge bg-warning text-dark">Complaint</span>
                                    @break
                                @case('suggestion')
                                    <span class="badge bg-success">Suggestion</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">Other</span>
                            @endswitch
                        </td>
                        <td>
                            <div style="max-width: 250px;">
                                {{ Str::limit($item->message, 100) }}
                            </div>
                        </td>
                        <td>
                            @if($item->is_read)
                                <span class="status-badge bg-success text-white">Read</span>
                                @if($item->read_at)
                                    <br><small class="text-muted">{{ $item->read_at->format('M d, Y') }}</small>
                                @endif
                            @else
                                <span class="status-badge bg-warning text-dark">Unread</span>
                            @endif
                        </td>
                        <td>
                            {{ $item->created_at->format('M d, Y') }}
                            <br><small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.feedback.show', $item) }}" class="btn btn-sm btn-primary btn-action">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                
                                @if(!$item->is_read)
                                    <form action="{{ route('admin.feedback.mark-read', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success btn-action">
                                            <i class="fas fa-check"></i> Mark Read
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('admin.feedback.delete', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-action" 
                                            onclick="return confirm('Delete this feedback?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No feedback found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $feedback->links() }}
    </div>
</div>
@endsection
