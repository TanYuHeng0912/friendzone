<?php

namespace App\Http\Controllers;

use App\UserMatch;
use App\User;
use Illuminate\Http\Request;

class TestMatchController extends Controller
{
    public function test()
    {
        $user = auth()->user();
        
        // Get all matches for this user
        $matchesAsUserOne = UserMatch::where('user_one', $user->id)->get();
        $matchesAsUserTwo = UserMatch::where('user_two', $user->id)->get();
        
        // Test the exclusion query
        $excludedUsers = User::query()
            ->whereNotExists(function ($subquery) use ($user) {
                $subquery->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('matches')
                    ->whereColumn('matches.user_two', 'users.id')
                    ->where('matches.user_one', $user->id);
            })
            ->pluck('id');
        
        // Test the matches query
        $matchedUsers = User::searchMatches($user->id)->get();
        
        return response()->json([
            'user_id' => $user->id,
            'matches_as_user_one' => $matchesAsUserOne->map(function($m) {
                return ['id' => $m->id, 'user_one' => $m->user_one, 'user_two' => $m->user_two];
            }),
            'matches_as_user_two' => $matchesAsUserTwo->map(function($m) {
                return ['id' => $m->id, 'user_one' => $m->user_one, 'user_two' => $m->user_two];
            }),
            'excluded_user_ids' => $excludedUsers->toArray(),
            'matched_users_count' => $matchedUsers->count(),
            'matched_user_ids' => $matchedUsers->pluck('id')->toArray()
        ]);
    }
}

