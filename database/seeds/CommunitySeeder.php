<?php

use Illuminate\Database\Seeder;
use App\Community;

class CommunitySeeder extends Seeder
{
    public function run()
    {
        $communities = [
            [
                'name' => 'Movie Enthusiasts',
                'slug' => 'movie',
                'description' => 'Discuss your favorite films, movie reviews, and cinema culture.',
                'icon' => '🎬'
            ],
            [
                'name' => 'Travel Adventures',
                'slug' => 'travelling',
                'description' => 'Share travel experiences, tips, and discover new destinations.',
                'icon' => '✈️'
            ],
            [
                'name' => 'Art Gallery',
                'slug' => 'art',
                'description' => 'Showcase your artwork, discuss techniques, and appreciate creativity.',
                'icon' => '🎨'
            ],
            [
                'name' => 'Sports Arena',
                'slug' => 'sport',
                'description' => 'Talk about your favorite sports, teams, and athletic achievements.',
                'icon' => '⚽'
            ],
            [
                'name' => 'Cooking Corner',
                'slug' => 'cooking',
                'description' => 'Share recipes, cooking tips, and culinary adventures.',
                'icon' => '👨‍🍳'
            ],
            [
                'name' => 'Gaming Hub',
                'slug' => 'gaming',
                'description' => 'Discuss games, share gameplay experiences, and connect with fellow gamers.',
                'icon' => '🎮'
            ],
            [
                'name' => 'Anime Community',
                'slug' => 'anime',
                'description' => 'Talk about anime series, manga, and Japanese culture.',
                'icon' => '🌸'
            ],
            [
                'name' => 'Book Club',
                'slug' => 'reading',
                'description' => 'Share book recommendations, reviews, and literary discussions.',
                'icon' => '📚'
            ],
            [
                'name' => 'Volunteer Network',
                'slug' => 'volunteering',
                'description' => 'Share volunteer opportunities and community service experiences.',
                'icon' => '🤝'
            ],
            [
                'name' => 'Photography Studio',
                'slug' => 'photography',
                'description' => 'Share your photographs, techniques, and visual storytelling.',
                'icon' => '📸'
            ]
        ];

        foreach ($communities as $community) {
            Community::create($community);
        }
    }
}