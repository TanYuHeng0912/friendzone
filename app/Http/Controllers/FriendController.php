<?php

namespace App\Http\Controllers;

use App\Friendship;
use App\Activity;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FriendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function sendRequest(Request $request, $userId): JsonResponse
    {
        $user = auth()->user();
        $friend = User::findOrFail($userId);
        
        if ($user->id === $friend->id) {
            return response()->json(['error' => 'Cannot send friend request to yourself'], 400);
        }
        
        // Check if friendship already exists
        $existing = Friendship::where(function($query) use ($user, $friend) {
            $query->where('user_id', $user->id)->where('friend_id', $friend->id);
        })->orWhere(function($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)->where('friend_id', $user->id);
        })->first();
        
        if ($existing) {
            return response()->json(['error' => 'Friendship already exists'], 400);
        }
        
        $friendship = Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id,
            'requester_id' => $user->id,
            'status' => 'pending'
        ]);
        
        // Create activity
        Activity::createActivity($user->id, 'friend_request', "Sent friend request to {$friend->info->name}", $friendship->id, 'App\Friendship');
        
        return response()->json(['success' => true, 'friendship' => $friendship]);
    }
    
    public function acceptRequest($friendshipId): JsonResponse
    {
        $user = auth()->user();
        $friendship = Friendship::findOrFail($friendshipId);
        
        if ($friendship->friend_id !== $user->id || $friendship->status !== 'pending') {
            return response()->json(['error' => 'Invalid request'], 400);
        }
        
        $friendship->update(['status' => 'accepted']);
        
        // Create activity
        Activity::createActivity($user->id, 'friend_accepted', "Accepted friend request from {$friendship->requester->info->name}", $friendship->id, 'App\Friendship');
        Activity::createActivity($friendship->requester_id, 'friend_accepted', "{$user->info->name} accepted your friend request", $friendship->id, 'App\Friendship');
        
        return response()->json(['success' => true, 'friendship' => $friendship]);
    }
    
    public function rejectRequest($friendshipId): JsonResponse
    {
        $user = auth()->user();
        $friendship = Friendship::findOrFail($friendshipId);
        
        if ($friendship->friend_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $friendship->delete();
        
        return response()->json(['success' => true]);
    }
    
    public function removeFriend($friendshipId): JsonResponse
    {
        $user = auth()->user();
        $friendship = Friendship::findOrFail($friendshipId);
        
        if ($friendship->user_id !== $user->id && $friendship->friend_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $friendship->delete();
        
        return response()->json(['success' => true]);
    }
    
    public function getFriends(): JsonResponse
    {
        $user = auth()->user();
        
        $friendships = Friendship::where(function($query) use ($user) {
            $query->where('user_id', $user->id)->orWhere('friend_id', $user->id);
        })->where('status', 'accepted')
        ->with(['user.info', 'friend.info'])
        ->get();
        
        $friends = $friendships->map(function($friendship) use ($user) {
            $friend = $friendship->user_id === $user->id ? $friendship->friend : $friendship->user;
            return [
                'id' => $friend->id,
                'name' => $friend->info->name ?? 'User',
                'picture' => $friend->info->getPicture(),
                'is_online' => $friend->isOnline()
            ];
        });
        
        return response()->json(['friends' => $friends]);
    }
    
    public function getPendingRequests(): JsonResponse
    {
        $user = auth()->user();
        
        $requests = Friendship::where('friend_id', $user->id)
            ->where('status', 'pending')
            ->with(['requester.info'])
            ->get();
        
        return response()->json(['requests' => $requests]);
    }
}
