<?php

namespace App\Http\Controllers;

use App\Poll;
use App\PollVote;
use App\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function vote(Request $request, $pollId): JsonResponse
    {
        $request->validate([
            'option_index' => 'required|integer|min:0'
        ]);
        
        $user = auth()->user();
        $poll = Poll::findOrFail($pollId);
        
        // Check if poll is expired
        if ($poll->isExpired()) {
            return response()->json(['error' => 'This poll has expired'], 400);
        }
        
        // Check if user already voted
        if ($poll->hasVoted($user->id)) {
            return response()->json(['error' => 'You have already voted on this poll'], 400);
        }
        
        // Validate option index
        if ($request->option_index >= count($poll->options)) {
            return response()->json(['error' => 'Invalid option'], 400);
        }
        
        PollVote::create([
            'poll_id' => $poll->id,
            'user_id' => $user->id,
            'option_index' => $request->option_index
        ]);
        
        // Create activity
        Activity::createActivity($user->id, 'poll_vote', "Voted on poll: {$poll->question}", $poll->id, 'App\Poll');
        
        $results = $poll->getResults();
        
        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
    
    public function getResults($pollId): JsonResponse
    {
        $poll = Poll::findOrFail($pollId);
        $results = $poll->getResults();
        
        return response()->json([
            'results' => $results,
            'has_voted' => $poll->hasVoted(auth()->id()),
            'is_expired' => $poll->isExpired()
        ]);
    }
}
