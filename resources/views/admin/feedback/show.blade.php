@extends('layouts.adminapp')

@section('page-title', 'Feedback Details')
@section('page-subtitle', 'View feedback information')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-comments"></i> Feedback Details</h4>
                <a href="{{ route('admin.feedback') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Feedback
                </a>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="stat-card" style="border-left-color: #667eea;">
                        <h6 class="mb-3"><i class="fas fa-user"></i> User Information</h6>
                        @if($feedback->user)
                            <div class="d-flex align-items-center gap-3 mb-3">
                                @if($feedback->user->info && $feedback->user->info->profile_picture)
                                    <img src="{{ asset('storage/' . $feedback->user->info->profile_picture) }}" 
                                         alt="Profile" class="user-avatar">
                                @else
                                    <div class="user-avatar bg-secondary d-flex align-items-center justify-content-center text-white">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ $feedback->user->info->first_name ?? 'Unknown' }} {{ $feedback->user->info->last_name ?? '' }}</strong>
                                    <br><small class="text-muted">{{ $feedback->user->email }}</small>
                                </div>
                            </div>
                            <p><strong>Member Since:</strong> {{ $feedback->user->created_at->format('M d, Y') }}</p>
                            <p><strong>User ID:</strong> {{ $feedback->user->id }}</p>
                        @else
                            <p class="text-muted">User account has been deleted</p>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="stat-card" style="border-left-color: #48bb78;">
                        <h6 class="mb-3"><i class="fas fa-info-circle"></i> Feedback Information</h6>
                        <p><strong>Type:</strong> 
                            @switch($feedback->type)
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
                        </p>
                        <p><strong>Submitted:</strong> {{ $feedback->created_at->format('M d, Y H:i') }}</p>
                        <p><strong>Status:</strong> 
                            @if($feedback->is_read)
                                <span class="badge bg-success">Read</span>
                                @if($feedback->read_at)
                                    <br><small class="text-muted">Read on: {{ $feedback->read_at->format('M d, Y H:i') }}</small>
                                @endif
                            @else
                                <span class="badge bg-warning text-dark">Unread</span>
                            @endif
                        </p>
                        <p><strong>Feedback ID:</strong> #{{ $feedback->id }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <h6 class="mb-3"><i class="fas fa-heading"></i> Subject</h6>
                <p class="fs-5">{{ $feedback->subject }}</p>
            </div>

            <div class="stat-card mt-3">
                <h6 class="mb-3"><i class="fas fa-comment-dots"></i> Message</h6>
                <div class="p-4 bg-light rounded" style="white-space: pre-wrap; line-height: 1.6;">
                    {{ $feedback->message }}
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.feedback') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Feedback List
                </a>
                <div>
                    @if(!$feedback->is_read)
                        <form action="{{ route('admin.feedback.mark-read', $feedback) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Mark as Read
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.feedback.delete', $feedback) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this feedback?')">
                            <i class="fas fa-trash"></i> Delete Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

