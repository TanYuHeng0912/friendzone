<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventRsvp;
use App\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function rsvp(Request $request, $eventId): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:going,interested,not_going'
        ]);
        
        $user = auth()->user();
        $event = Event::findOrFail($eventId);
        
        // Check if event is full
        if ($request->status === 'going' && $event->isFull()) {
            return response()->json(['error' => 'This event is full'], 400);
        }
        
        // Check if event is past
        if ($event->isPast()) {
            return response()->json(['error' => 'This event has already passed'], 400);
        }
        
        $rsvp = EventRsvp::updateOrCreate(
            [
                'event_id' => $event->id,
                'user_id' => $user->id
            ],
            [
                'status' => $request->status
            ]
        );
        
        // Create activity
        Activity::createActivity($user->id, 'event_rsvp', "RSVP'd to event: {$event->title}", $event->id, 'App\Event');
        
        return response()->json([
            'success' => true,
            'rsvp' => $rsvp,
            'going_count' => $event->goingCount(),
            'interested_count' => $event->interestedCount()
        ]);
    }
    
    public function cancelRsvp($eventId): JsonResponse
    {
        $user = auth()->user();
        $event = Event::findOrFail($eventId);
        
        EventRsvp::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->delete();
        
        return response()->json([
            'success' => true,
            'going_count' => $event->goingCount(),
            'interested_count' => $event->interestedCount()
        ]);
    }
    
    public function getRsvps($eventId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        
        $rsvps = $event->rsvps()
            ->with('user.info')
            ->get();
        
        return response()->json([
            'rsvps' => $rsvps,
            'going_count' => $event->goingCount(),
            'interested_count' => $event->interestedCount(),
            'user_rsvp' => $event->getUserRsvp(auth()->id())
        ]);
    }
}
