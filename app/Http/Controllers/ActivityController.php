<?php

namespace App\Http\Controllers;

use App\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(): View
    {
        $user = auth()->user();
        
        // Get activities from user and their friends
        $friendIds = \App\Friendship::where(function($query) use ($user) {
            $query->where('user_id', $user->id)->orWhere('friend_id', $user->id);
        })->where('status', 'accepted')
        ->get()
        ->map(function($friendship) use ($user) {
            return $friendship->user_id === $user->id ? $friendship->friend_id : $friendship->user_id;
        })
        ->push($user->id)
        ->toArray();
        
        $activities = Activity::whereIn('user_id', $friendIds)
            ->with('user.info')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('activity.index', compact('activities'));
    }
    
    public function api(): JsonResponse
    {
        $user = auth()->user();
        
        $activities = Activity::where('user_id', $user->id)
            ->orWhereIn('user_id', function($query) use ($user) {
                // Get friend IDs
                $query->select('friend_id')
                    ->from('friendships')
                    ->where('user_id', $user->id)
                    ->where('status', 'accepted')
                    ->union(
                        \DB::table('friendships')
                            ->select('user_id')
                            ->where('friend_id', $user->id)
                            ->where('status', 'accepted')
                    );
            })
            ->with('user.info')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        return response()->json(['activities' => $activities]);
    }
}
