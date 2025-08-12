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
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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
        $post->save();

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