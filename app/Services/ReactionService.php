<?php

namespace App\Services;

use App\Dislike;
use App\Mail\SendMatchedEmail;
use App\UserMatch;
use App\User;
use Illuminate\Support\Facades\Mail;

class ReactionService
{
    public function like($user, $otherUser, $isSuperLike = false)
    {
        // Check if already liked
        $existingMatch = UserMatch::where('user_one', $user->id)
            ->where('user_two', $otherUser->id)
            ->first();

        if ($existingMatch) {
            \Log::info('User already liked', ['user_id' => $user->id, 'other_user_id' => $otherUser->id]);
            return ['matched' => false, 'already_liked' => true];
        }

        // Create the like
        try {
            $match = UserMatch::create([
                'user_one' => $user->id,
                'user_two' => $otherUser->id,
                'is_super_like' => $isSuperLike
            ]);
            \Log::info('Match created', ['match_id' => $match->id, 'user_one' => $user->id, 'user_two' => $otherUser->id]);
        } catch (\Exception $e) {
            \Log::error('Failed to create match', ['error' => $e->getMessage(), 'user_id' => $user->id, 'other_user_id' => $otherUser->id]);
            return ['matched' => false, 'error' => 'Failed to save like'];
        }

        // Check if it's a match (other user already liked this user)
        $isMatch = UserMatch::where('user_one', $otherUser->id)
            ->where('user_two', $user->id)
            ->exists();

        \Log::info('Checking for mutual match', [
            'user_id' => $user->id,
            'other_user_id' => $otherUser->id,
            'is_match' => $isMatch
        ]);

        if ($isMatch) {
            // Send match email
            try {
                Mail::to($otherUser->email)->send(new SendMatchedEmail($user, $otherUser));
                Mail::to($user->email)->send(new SendMatchedEmail($otherUser, $user));
            } catch (\Exception $e) {
                // Log error but don't fail
                \Log::error('Failed to send match email: ' . $e->getMessage());
            }

            return [
                'matched' => true,
                'match_user' => $otherUser,
                'is_super_like' => $isSuperLike
            ];
        }

        return ['matched' => false, 'is_super_like' => $isSuperLike];
    }

    public function dislike($user, $otherUser)
    {
        // Check if already disliked
        $existingDislike = Dislike::where('user_one', $user->id)
            ->where('user_two', $otherUser->id)
            ->first();

        if ($existingDislike) {
            return ['already_disliked' => true];
        }

        Dislike::create([
            'user_one' => $user->id,
            'user_two' => $otherUser->id
        ]);

        return ['disliked' => true];
    }

    public function calculateCompatibility($user1, $user2)
    {
        $score = 0;
        $maxScore = 0;

        // Age compatibility (20 points)
        $maxScore += 20;
        $ageDiff = abs($user1->info->age - $user2->info->age);
        if ($ageDiff <= 2) {
            $score += 20;
        } elseif ($ageDiff <= 5) {
            $score += 15;
        } elseif ($ageDiff <= 10) {
            $score += 10;
        } else {
            $score += 5;
        }

        // Common interests (30 points)
        $maxScore += 30;
        $tags1 = array_filter([$user1->info->tag1, $user1->info->tag2, $user1->info->tag3], function($tag) {
            return $tag && $tag !== 'none';
        });
        $tags2 = array_filter([$user2->info->tag1, $user2->info->tag2, $user2->info->tag3], function($tag) {
            return $tag && $tag !== 'none';
        });
        $commonTags = count(array_intersect($tags1, $tags2));
        if ($commonTags > 0) {
            $score += ($commonTags / max(count($tags1), count($tags2))) * 30;
        }

        // Country match (10 points)
        $maxScore += 10;
        if ($user1->info->country === $user2->info->country) {
            $score += 10;
        }

        // Language match (20 points)
        $maxScore += 20;
        $langs1 = explode(',', $user1->info->languages);
        $langs2 = explode(',', $user2->info->languages);
        $commonLangs = count(array_intersect($langs1, $langs2));
        if ($commonLangs > 0) {
            $score += min(20, $commonLangs * 10);
        }

        // Relationship status compatibility (20 points)
        $maxScore += 20;
        if ($user1->info->relationship === $user2->info->relationship) {
            $score += 20;
        } elseif (in_array($user1->info->relationship, ['Single', 'Open relationship']) && 
                  in_array($user2->info->relationship, ['Single', 'Open relationship'])) {
            $score += 15;
        }

        return $maxScore > 0 ? round(($score / $maxScore) * 100) : 50;
    }
}

