@extends('layouts.adminapp')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-comments"></i> Feedback Management</h1>
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

            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-3" id="feedbackTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab">
                        All Feedback <span class="badge badge-secondary">{{ $feedback->total() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="unread-tab" data-toggle="tab" href="#unread" role="tab">
                        Unread <span class="badge badge-warning">{{ $feedback->where('is_read', false)->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="bug-tab" data-toggle="tab" href="#bug" role="tab">
                        Bug Reports <span class="badge badge-danger">{{ $feedback->where('type', 'bug_report')->count() }}</span>
                    </a>
                </li>
            </ul>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">User Feedback ({{ $feedback->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
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
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->user && $item->user->info && $item->user->info->profile_picture)
                                                    <img src="{{ asset('storage/' . $item->user->info->profile_picture) }}" 
                                                         alt="Profile" class="rounded-circle me-2" width="30" height="30">
                                                @else
                                                    <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 30px; height: 30px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    @if($item->user)
                                                        <strong>{{ $item->user->info->first_name ?? 'Unknown' }} {{ $item->user->info->last_name ?? '' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $item->user->email }}</small>
                                                    @else
                                                        <span class="text-muted">Deleted User</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $item->subject }}</strong>
                                            @if(!$item->is_read)
                                                <span class="badge badge-warning ml-1">New</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($item->type)
                                                @case('bug_report')
                                                    <span class="badge badge-danger">Bug Report</span>
                                                    @break
                                                @case('feature_request')
                                                    <span class="badge badge-info">Feature Request</span>
                                                    @break
                                                @case('complaint')
                                                    <span class="badge badge-warning">Complaint</span>
                                                    @break
                                                @case('suggestion')
                                                    <span class="badge badge-success">Suggestion</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">Other</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div style="max-width: 250px;">
                                                {{ Str::limit($item->message, 100) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->is_read)
                                                <span class="badge badge-success">Read</span>
                                                @if($item->read_at)
                                                    <br><small class="text-muted">{{ $item->read_at->format('M d, Y') }}</small>
                                                @endif
                                            @else
                                                <span class="badge badge-warning">Unread</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical" role="group">
                                                <button type="button" class="btn btn-sm btn-primary mb-1" 
                                                        data-toggle="modal" data-target="#viewModal{{ $item->id }}">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                
                                                @if(!$item->is_read)
                                                    <form action="{{ route('admin.feedback.mark-read', $item) }}" method="POST" class="d-inline mb-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i> Mark Read
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <form action="{{ route('admin.feedback.delete', $item) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this feedback?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- View Modal -->
                                            <div class="modal fade" id="viewModal{{ $item->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Feedback Details</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6>User Information</h6>
                                                                    @if($item->user)
                                                                        <p><strong>Name:</strong> {{ $item->user->info->first_name ?? 'N/A' }} {{ $item->user->info->last_name ?? '' }}</p>
                                                                        <p><strong>Email:</strong> {{ $item->user->email }}</p>
                                                                        <p><strong>Member Since:</strong> {{ $item->user->created_at->format('M d, Y') }}</p>
                                                                    @else
                                                                        <p class="text-muted">User account has been deleted</p>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6>Feedback Information</h6>
                                                                    <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $item->type)) }}</p>
                                                                    <p><strong>Submitted:</strong> {{ $item->created_at->format('M d, Y H:i') }}</p>
                                                                    <p><strong>Status:</strong> {{ $item->is_read ? 'Read' : 'Unread' }}</p>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <h6>Subject</h6>
                                                            <p>{{ $item->subject }}</p>
                                                            <h6>Message</h6>
                                                            <div class="border p-3 bg-light">
                                                                {{ $item->message }}
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            @if(!$item->is_read)
                                                                <form action="{{ route('admin.feedback.mark-read', $item) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="btn btn-success">Mark as Read</button>
                                                                </form>
                                                            @endif
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No feedback found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $feedback->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection