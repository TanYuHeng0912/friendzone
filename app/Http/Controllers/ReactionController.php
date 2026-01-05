<?php

namespace App\Http\Controllers;

use App\Services\ReactionService;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    private ReactionService $reactionService;

    public function __construct(ReactionService $reactionService)
    {
        $this->middleware('auth');
        $this->reactionService = $reactionService;
    }

    public function like(int $id, Request $request): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $otherUser = User::findOrFail($id);

        $isSuperLike = $request->input('super_like', false);
        $result = $this->reactionService->like($user, $otherUser, $isSuperLike);

        // Log for debugging
        \Log::info('Like action', [
            'user_id' => $user->id,
            'other_user_id' => $otherUser->id,
            'result' => $result
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            $compatibility = $this->reactionService->calculateCompatibility($user, $otherUser);
            return response()->json([
                'success' => true,
                'matched' => $result['matched'] ?? false,
                'is_super_like' => $result['is_super_like'] ?? false,
                'compatibility' => $compatibility,
                'match_user' => $result['matched'] ? [
                    'id' => $result['match_user']->id,
                    'name' => $result['match_user']->info->name . ' ' . $result['match_user']->info->surname,
                    'picture' => $result['match_user']->info->getPicture()
                ] : null
            ]);
        }

        return redirect(route('home'));
    }

    public function dislike(int $id, Request $request): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $otherUser = User::findOrFail($id);

        $result = $this->reactionService->dislike($user, $otherUser);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'disliked' => $result['disliked'] ?? false
            ]);
        }

        return redirect(route('home'));
    }

    public function getCompatibility(int $id): JsonResponse
    {
        $user = auth()->user();
        $otherUser = User::findOrFail($id);

        $compatibility = $this->reactionService->calculateCompatibility($user, $otherUser);

        return response()->json([
            'compatibility' => $compatibility
        ]);
    }
}

