<?php

namespace App\Services;

use App\User;
use App\UserSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SmartMatchingService
{
    /**
     * Get the best matching user with smart ranking
     */
    public function getBestMatch($user, UserSettings $userSettings): ?User
    {
        $userInfo = $user->info;
        
        // Determine gender filter
        // If both are checked or both are unchecked, search for both genders
        $gender = 'both';
        if ($userSettings->search_female == 1 && ($userSettings->search_male != 1 || $userSettings->search_male == 0)) {
            $gender = 'female';
        } elseif ($userSettings->search_male == 1 && ($userSettings->search_female != 1 || $userSettings->search_female == 0)) {
            $gender = 'male';
        }

        // Build base query
        $query = User::query()
            ->searchWithSettings(
                $userSettings->search_age_from,
                $userSettings->search_age_to,
                $gender,
                $user->id,
                $userSettings->search_tag1,
                $userSettings->search_tag2,
                $userSettings->search_tag3
            )
            ->searchWithoutLikesAndDislikes($user->id);

        // Advanced filters
        if ($userSettings->search_country) {
            $query->whereHas('info', function($q) use ($userSettings) {
                $q->where('country', $userSettings->search_country);
            });
        }

        if ($userSettings->search_relationship) {
            $query->whereHas('info', function($q) use ($userSettings) {
                $q->where('relationship', $userSettings->search_relationship);
            });
        }

        if ($userSettings->search_has_photos == '1') {
            $query->whereHas('info', function($q) {
                $q->where('profile_picture', '!=', '')->whereNotNull('profile_picture');
            })->whereHas('pictures', function($q) {
                $q->where('path', '!=', '');
            }, '>=', 1);
        }

        // Get recently shown users to avoid repetition
        // Note: Temporarily disabled for debugging - re-enable after testing
        // $recentlyShown = $this->getRecentlyShownUsers($user->id);
        // if (!empty($recentlyShown)) {
        //     $query->whereNotIn('users.id', $recentlyShown);
        // }
        $recentlyShown = [];

        // Get user's tags and preferences for scoring
        $userTags = array_filter([
            $userInfo->tag1,
            $userInfo->tag2,
            $userInfo->tag3
        ], function($tag) {
            return $tag && $tag !== 'none';
        });

        $searchTags = array_filter([
            $userSettings->search_tag1,
            $userSettings->search_tag2,
            $userSettings->search_tag3
        ], function($tag) {
            return $tag && $tag !== 'none' && $tag !== null;
        });

        // Calculate match score using raw SQL for performance
        // Note: searchWithSettings uses whereHas which doesn't join user_infos, so we need to join it for scoring
        $matchScoreSQL = $this->getMatchScoreSQL($userInfo, $userTags, $searchTags);
        
        // IMPORTANT: Apply exclusion AFTER building base query but BEFORE joins
        // This ensures liked users are excluded even after joins
        // The exclusion is already applied via searchWithoutLikesAndDislikes above
        
        // Check if user_infos is already joined (it shouldn't be from whereHas, but check to be safe)
        $query->select('users.*')
            ->selectRaw($matchScoreSQL)
            ->join('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->leftJoin('matches', function($join) use ($user) {
                $join->on('matches.user_one', '=', 'users.id')
                     ->where('matches.user_two', '=', $user->id);
            })
            ->distinct() // Ensure no duplicate users
            ->orderByRaw('match_score DESC, RAND()'); // Add randomness within same score

        // Get top 5 candidates and randomly pick one for variety
        try {
            $candidates = $query->limit(5)->get();
        } catch (\Exception $e) {
            // If there's an error (like SQL syntax), fall back to simpler query
            \Log::error('Smart matching query error: ' . $e->getMessage());
            
            // Fallback: remove scoring and just get a random match
            $fallbackQuery = User::query()
                ->searchWithSettings(
                    $userSettings->search_age_from,
                    $userSettings->search_age_to,
                    $gender,
                    $user->id,
                    $userSettings->search_tag1,
                    $userSettings->search_tag2,
                    $userSettings->search_tag3
                )
                ->searchWithoutLikesAndDislikes($user->id);
            
            if (!empty($recentlyShown)) {
                $fallbackQuery->whereNotIn('users.id', $recentlyShown);
            }
            
            return $fallbackQuery->inRandomOrder()->first();
        }
        
        if ($candidates->isEmpty()) {
            return null;
        }

        // Return random from top candidates (adds diversity)
        return $candidates->random();
    }

    /**
     * Generate SQL for calculating match score
     */
    private function getMatchScoreSQL($userInfo, $userTags, $searchTags): string
    {
        $userAge = (int)($userInfo->age ?? 25);
        $userCountry = DB::getPdo()->quote($userInfo->country ?? '');
        $userLanguages = $userInfo->languages ?? '';
        $userRelationship = DB::getPdo()->quote($userInfo->relationship ?? 'Single');

        // Age compatibility (closer = better, max 30 points)
        $ageScore = "
            CASE 
                WHEN ABS(user_infos.age - {$userAge}) <= 2 THEN 30
                WHEN ABS(user_infos.age - {$userAge}) <= 5 THEN 20
                WHEN ABS(user_infos.age - {$userAge}) <= 10 THEN 15
                ELSE 5
            END
        ";

        // Common interests/tags (max 30 points)
        $tagScore = "0";
        if (!empty($searchTags)) {
            $tagScoreParts = [];
            foreach ($searchTags as $tag) {
                $escapedTag = DB::getPdo()->quote($tag);
                $tagScoreParts[] = "(CASE WHEN (user_infos.tag1 = {$escapedTag} OR user_infos.tag2 = {$escapedTag} OR user_infos.tag3 = {$escapedTag}) THEN 10 ELSE 0 END)";
            }
            if (!empty($tagScoreParts)) {
                $tagScore = "LEAST(" . implode(" + ", $tagScoreParts) . ", 30)";
            }
        }

        // Country match (10 points)
        $countryScore = $userInfo->country ? "CASE WHEN user_infos.country = {$userCountry} THEN 10 ELSE 0 END" : "0";

        // Language match (20 points)
        $languageScore = "0";
        if ($userLanguages) {
            $langs = array_filter(array_map('trim', explode(',', $userLanguages)));
            $langConditions = [];
            foreach ($langs as $lang) {
                if ($lang) {
                    $escapedLang = DB::getPdo()->quote($lang);
                    $langConditions[] = "FIND_IN_SET({$escapedLang}, user_infos.languages) > 0";
                }
            }
            if (!empty($langConditions)) {
                $languageScore = "CASE WHEN (" . implode(" OR ", $langConditions) . ") THEN 20 ELSE 0 END";
            }
        }

        // Relationship status compatibility (10 points)
        $relationshipScore = "CASE 
            WHEN user_infos.relationship = {$userRelationship} THEN 10
            WHEN user_infos.relationship IN ('Single', 'Open relationship') AND {$userRelationship} IN ('Single', 'Open relationship') THEN 7
            ELSE 0
        END";

        // Profile completeness bonus (10 points)
        $completenessScore = "
            (CASE WHEN user_infos.profile_picture IS NOT NULL AND user_infos.profile_picture != '' THEN 3 ELSE 0 END) +
            (CASE WHEN user_infos.description IS NOT NULL AND user_infos.description != '' THEN 3 ELSE 0 END) +
            (CASE WHEN user_infos.languages IS NOT NULL AND user_infos.languages != '' THEN 2 ELSE 0 END) +
            (CASE WHEN user_infos.country IS NOT NULL AND user_infos.country != '' THEN 2 ELSE 0 END)
        ";

        // Activity bonus (10 points)
        $activityScore = "
            CASE 
                WHEN users.is_online = 1 THEN 10
                WHEN users.last_seen_at > DATE_SUB(NOW(), INTERVAL 1 DAY) THEN 8
                WHEN users.last_seen_at > DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 5
                WHEN users.last_seen_at > DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 2
                ELSE 0
            END
        ";

        // Mutual interest bonus (50 points) - if they liked you, show them first!
        $mutualInterestScore = "CASE WHEN matches.id IS NOT NULL THEN 50 ELSE 0 END";

        // Combine all scores
        return "(
            {$ageScore} +
            {$tagScore} +
            {$countryScore} +
            {$languageScore} +
            {$relationshipScore} +
            {$completenessScore} +
            {$activityScore} +
            {$mutualInterestScore}
        ) as match_score";
    }

    /**
     * Get recently shown users to avoid repetition
     */
    private function getRecentlyShownUsers($userId): array
    {
        $cacheKey = "recently_shown_users_{$userId}";
        return Cache::get($cacheKey, []);
    }

    /**
     * Mark user as recently shown
     */
    public function markAsShown($userId, $shownUserId): void
    {
        $cacheKey = "recently_shown_users_{$userId}";
        $recent = Cache::get($cacheKey, []);
        
        // Add to recent list
        $recent[] = $shownUserId;
        
        // Keep only last 10 (reduced from 20 to allow more variety)
        $recent = array_slice($recent, -10);
        
        // Cache for 1 hour (reduced from 24 hours for better matching)
        Cache::put($cacheKey, $recent, now()->addHours(1));
    }
    
    /**
     * Clear recently shown users cache for a user (useful for testing)
     */
    public function clearRecentlyShown($userId): void
    {
        $cacheKey = "recently_shown_users_{$userId}";
        Cache::forget($cacheKey);
    }
}
