<?php

namespace App\Services;

use App\User;
use App\UserSettings;

class RandomUserService
{
    private SmartMatchingService $smartMatchingService;

    public function __construct(SmartMatchingService $smartMatchingService)
    {
        $this->smartMatchingService = $smartMatchingService;
    }

    public function getUser($user, UserSettings $userSettings): ?User
    {
        // Use smart matching instead of random
        $matchedUser = $this->smartMatchingService->getBestMatch($user, $userSettings);
        
        if ($matchedUser) {
            // Mark as shown to avoid repetition
            $this->smartMatchingService->markAsShown($user->id, $matchedUser->id);
        }
        
        return $matchedUser;
    }
}
