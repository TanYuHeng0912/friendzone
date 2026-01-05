@extends('layouts.adminapp')

@section('page-title', 'Community Management')
@section('page-subtitle', 'Manage all communities')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-users-cog"></i> Community Management</h2>
        <p class="text-muted mb-0">Total: {{ $communities->total() }} communities</p>
    </div>
    <div>
        <a href="{{ route('admin.communities.create') }}" class="btn btn-gradient">
            <i class="fas fa-plus"></i> Create Community
        </a>
    </div>
</div>

<div class="stat-card">
    <div class="table-responsive">
        <table class="admin-table table">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Members</th>
                    <th>Posts</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($communities as $community)
                    <tr>
                        <td>
                            @if($community->icon)
                                <img src="{{ asset('storage/' . $community->icon) }}" 
                                     alt="{{ $community->name }}" 
                                     style="width: 50px; height: 50px; border-radius: 10px; object-fit: cover;">
                            @else
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="fas fa-users"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $community->name }}</strong>
                            <br><small class="text-muted">{{ $community->slug }}</small>
                        </td>
                        <td>
                            <div style="max-width: 300px;">
                                {{ Str::limit($community->description, 80) }}
                            </div>
                        </td>
                        <td><span class="badge bg-info">{{ $community->members_count }}</span></td>
                        <td><span class="badge bg-info">{{ $community->posts_count }}</span></td>
                        <td>
                            {{ $community->created_at->format('M d, Y') }}
                            <br><small class="text-muted">{{ $community->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.communities.edit', $community) }}" class="btn btn-sm btn-primary btn-action">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('community.show', $community) }}" class="btn btn-sm btn-info btn-action" target="_blank">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <form action="{{ route('admin.communities.delete', $community) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-action" 
                                            onclick="return confirm('Delete this community? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No communities found</p>
                            <a href="{{ route('admin.communities.create') }}" class="btn btn-gradient">
                                <i class="fas fa-plus"></i> Create First Community
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $communities->links() }}
    </div>
</div>
@endsection
