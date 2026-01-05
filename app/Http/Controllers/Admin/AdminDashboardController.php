<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Community;
use App\Feedback;
use App\Post;
use App\UserMatch;
use App\Chat;
use App\Message;
use App\Friendship;
use App\Activity;
use App\Poll;
use App\Event;
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
        // Basic stats
        $stats = [
            'users_count' => User::count(),
            'communities_count' => Community::count(),
            'feedback_count' => Feedback::count(),
            'posts_count' => Post::count(),
            'matches_count' => UserMatch::count(),
            'chats_count' => Chat::count(),
            'messages_count' => Message::count(),
            'friendships_count' => Friendship::where('status', 'accepted')->count(),
            'polls_count' => Poll::count(),
            'events_count' => Event::count(),
            'active_users_today' => User::whereDate('updated_at', today())->count(),
            'online_users' => User::where('is_online', true)->count(),
            'new_users_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];
        
        // User growth chart data (last 30 days)
        $userGrowth = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $userGrowth[] = [
                'date' => $date->format('M d'),
                'count' => User::whereDate('created_at', $date->toDateString())->count()
            ];
        }
        
        // Activity breakdown
        $activityBreakdown = Activity::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();
        
        // Post type breakdown
        $postTypeBreakdown = Post::select('post_type', DB::raw('count(*) as count'))
            ->groupBy('post_type')
            ->get();
        
        // Top communities by members
        $topCommunities = Community::withCount('members')
            ->orderBy('members_count', 'desc')
            ->take(10)
            ->get();
        
        // User engagement metrics
        $engagement = [
            'avg_posts_per_user' => round(Post::count() / max(User::count(), 1), 2),
            'avg_messages_per_chat' => round(Message::count() / max(Chat::count(), 1), 2),
            'match_rate' => round((UserMatch::count() / max(User::count() * 2, 1)) * 100, 2),
            'active_communities' => Community::has('posts')->count()
        ];
        
        // Recent activities
        $recent_users = User::with('info')->latest()->take(5)->get();
        $recent_communities = Community::withCount(['members', 'posts'])->latest()->take(5)->get();
        $recent_feedback = Feedback::with('user')->latest()->take(5)->get();
        $recent_activities = Activity::with('user.info')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'stats', 
            'recent_users', 
            'recent_communities', 
            'recent_feedback',
            'recent_activities',
            'userGrowth',
            'activityBreakdown',
            'postTypeBreakdown',
            'topCommunities',
            'engagement'
        ));
    }

    // User Management
    public function users(Request $request)
    {
        $query = User::with('info')
            ->withCount(['posts', 'communities']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhereHas('info', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            switch($request->status) {
                case 'banned':
                    $query->where('is_banned', true);
                    break;
                case 'suspended':
                    $query->where('is_suspended', true)->where('suspended_until', '>', now());
                    break;
                case 'active':
                    $query->where('is_banned', false)
                          ->where(function($q) {
                              $q->where('is_suspended', false)
                                ->orWhere('suspended_until', '<=', now());
                          });
                    break;
                case 'admin':
                    $query->where('is_admin', true);
                    break;
            }
        }
        
        $users = $query->latest()->paginate(20)->appends($request->query());

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
    public function feedback(Request $request)
    {
        $query = Feedback::with('user');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Filter by read status
        if ($request->has('read_status') && $request->read_status) {
            if ($request->read_status == 'unread') {
                $query->where('is_read', false);
            } elseif ($request->read_status == 'read') {
                $query->where('is_read', true);
            }
        }
        
        $feedback = $query->latest()->paginate(20)->appends($request->query());
        
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