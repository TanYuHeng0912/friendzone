<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Community;
use App\Feedback;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is_admin']);
    }

    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'communities_count' => Community::count(),
            'feedback_count' => Feedback::count(),
            'posts_count' => Post::count(),
            'active_users_today' => User::whereDate('updated_at', today())->count(),
            'new_users_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        // Recent activities
        $recent_users = User::with('info')->latest()->take(5)->get();
        $recent_communities = Community::withCount(['members', 'posts'])->latest()->take(5)->get();
        $recent_feedback = Feedback::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_communities', 'recent_feedback'));
    }

    // User Management
    public function users()
    {
        $users = User::with('info')
            ->withCount(['posts', 'communities'])
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function banUser(User $user)
    {
        $user->update(['is_banned' => true, 'banned_at' => now()]);
        
        return redirect()->route('admin.users')->with('success', 'User has been banned successfully.');
    }

    public function unbanUser(User $user)
    {
        $user->update(['is_banned' => false, 'banned_at' => null]);
        
        return redirect()->route('admin.users')->with('success', 'User has been unbanned successfully.');
    }

    public function suspendUser(Request $request, User $user)
    {
        $request->validate([
            'suspension_days' => 'required|integer|min:1|max:365'
        ]);

        $user->update([
            'is_suspended' => true,
            'suspended_until' => now()->addDays($request->suspension_days)
        ]);
        
        return redirect()->route('admin.users')->with('success', 'User has been suspended for ' . $request->suspension_days . ' days.');
    }

    public function unsuspendUser(User $user)
    {
        $user->update(['is_suspended' => false, 'suspended_until' => null]);
        
        return redirect()->route('admin.users')->with('success', 'User suspension has been lifted.');
    }

    // Community Management
    public function communities()
    {
        $communities = Community::withCount(['members', 'posts'])->paginate(15);
        
        return view('admin.communities.index', compact('communities'));
    }

    public function createCommunity()
    {
        return view('admin.communities.create');
    }

    public function storeCommunity(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('community-icons', 'public');
        }

        Community::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description,
            'icon' => $iconPath
        ]);

        return redirect()->route('admin.communities')->with('success', 'Community created successfully.');
    }

    public function editCommunity(Community $community)
    {
        return view('admin.communities.edit', compact('community'));
    }

    public function updateCommunity(Request $request, Community $community)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description
        ];

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('community-icons', 'public');
        }

        $community->update($data);

        return redirect()->route('admin.communities')->with('success', 'Community updated successfully.');
    }

    public function deleteCommunity(Community $community)
    {
        $community->delete();
        
        return redirect()->route('admin.communities')->with('success', 'Community deleted successfully.');
    }

    // Feedback Management
    public function feedback()
    {
        $feedback = Feedback::with('user')->latest()->paginate(20);
        
        return view('admin.feedback.index', compact('feedback'));
    }

    public function showFeedback(Feedback $feedback)
    {
        return view('admin.feedback.show', compact('feedback'));
    }

    public function markFeedbackAsRead(Feedback $feedback)
    {
        $feedback->update(['is_read' => true, 'read_at' => now()]);
        
        return redirect()->route('admin.feedback')->with('success', 'Feedback marked as read.');
    }

    public function deleteFeedback(Feedback $feedback)
    {
        $feedback->delete();
        
        return redirect()->route('admin.feedback')->with('success', 'Feedback deleted successfully.');
    }
}