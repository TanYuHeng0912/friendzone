<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\View\View;

class MatchesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function matches(): View
    {
        $user = auth()->user();
        $id = $user->id;

        // Debug: Log all matches for this user
        $allMatches = \App\UserMatch::where('user_one', $id)
            ->orWhere('user_two', $id)
            ->get();
        \Log::info('All matches for user', ['user_id' => $id, 'matches' => $allMatches->toArray()]);

        $users = User::searchMatches($id)->get();
        
        \Log::info('Matched users found', ['user_id' => $id, 'count' => $users->count(), 'users' => $users->pluck('id')->toArray()]);

        return view('matches', [
            'users' => $users,
            'user' => $user,
        ]);
    }
}
