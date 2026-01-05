<?php

namespace App\Http\Controllers;

use App\Community;
use App\Post;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $communities = Community::withCount(['members', 'posts'])->get();
        $userCommunities = auth()->user()->communities()->pluck('community_id')->toArray();
        
        return view('community.index', compact('communities', 'userCommunities'));
    }

    public function show(Community $community)
    {
        $isMember = $community->isMember(auth()->id());
        
        if (!$isMember) {
            return redirect()->route('community.index')
                ->with('error', 'You need to join this community first.');
        }

        $posts = $community->posts()
            ->with(['user.info', 'community'])
            ->withCount(['comments', 'likes'])
            ->latest()
            ->paginate(10);

        return view('community.show', compact('community', 'posts'));
    }

    public function join(Community $community)
    {
        $user = auth()->user();
        
        if (!$community->isMember($user->id)) {
            $community->members()->attach($user->id);
        }

        return redirect()->back()->with('success', 'You have joined the ' . $community->name . ' community!');
    }

    public function leave(Community $community)
    {
        $user = auth()->user();
        
        if ($community->isMember($user->id)) {
            $community->members()->detach($user->id);
        }

        return redirect()->route('community.index')->with('success', 'You have left the ' . $community->name . ' community.');
    }

    public function createPost(Community $community)
    {
        $isMember = $community->isMember(auth()->id());
        
        if (!$isMember) {
            return redirect()->route('community.index')
                ->with('error', 'You need to join this community first.');
        }

        return view('community.create-post', compact('community'));
    }

    public function storePost(Request $request, Community $community)
    {
        // Default post_type to 'text' if not provided
        $request->merge(['post_type' => $request->input('post_type', 'text')]);
        
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'post_type' => 'required|in:text,image,video,poll,event',
            'poll_question' => 'required_if:post_type,poll',
            'poll_options' => 'required_if:post_type,poll|array|min:2',
            'event_title' => 'required_if:post_type,event',
            'event_start_time' => 'required_if:post_type,event',
            'event_location' => 'nullable|string|max:255'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post = new Post();
        $post->title = $request->title;
        $post->content = $request->content;
        $post->image = $imagePath;
        $post->user_id = auth()->id();
        $post->community_id = $community->id;
        $post->post_type = $request->post_type;
        $post->save();
        
        // Extract hashtags and mentions
        $post->saveHashtagsAndMentions();
        
        // Create poll if post type is poll
        if ($request->post_type === 'poll') {
            $poll = \App\Poll::create([
                'post_id' => $post->id,
                'question' => $request->poll_question,
                'options' => $request->poll_options,
                'ends_at' => $request->poll_ends_at ? now()->parse($request->poll_ends_at) : null
            ]);
        }
        
        // Create event if post type is event
        if ($request->post_type === 'event') {
            $event = \App\Event::create([
                'post_id' => $post->id,
                'community_id' => $community->id,
                'title' => $request->event_title,
                'description' => $request->content,
                'start_time' => now()->parse($request->event_start_time),
                'end_time' => $request->event_end_time ? now()->parse($request->event_end_time) : null,
                'location' => $request->event_location,
                'max_attendees' => $request->event_max_attendees
            ]);
        }
        
        // Create activity
        \App\Activity::createActivity(auth()->id(), 'post', "Created a new {$request->post_type} post in {$community->name}", $post->id, 'App\Post');

        return redirect()->route('community.show', $community)
            ->with('success', 'Post created successfully!');
    }

    public function showPost(Community $community, Post $post)
    {
        $isMember = $community->isMember(auth()->id());
        
        if (!$isMember) {
            return redirect()->route('community.index')
                ->with('error', 'You need to join this community first.');
        }

        $post->load(['user.info', 'community', 'comments.user.info', 'comments.replies.user.info']);
        
        return view('community.post', compact('community', 'post'));
    }

    public function likePost(Post $post)
    {
        $user = auth()->user();
        
        if ($post->isLikedBy($user->id)) {
            $post->likes()->detach($user->id);
        } else {
            $post->likes()->attach($user->id);
        }

        // Get the current likes count
        $likesCount = $post->likes()->count();
        
        // Update the likes count in the database
        $post->update(['likes_count' => $likesCount]);

        return response()->json([
            'liked' => $post->isLikedBy($user->id),
            'likes_count' => $likesCount
        ]);
    }

    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = auth()->id();
        $comment->post_id = $post->id;
        $comment->parent_id = $request->parent_id;
        $comment->save();

        $post->updateCommentsCount();

        return redirect()->back()->with('success', 'Comment added successfully!');
    }
}