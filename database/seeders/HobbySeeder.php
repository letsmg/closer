<?php

namespace Database\Seeders;

use App\Models\Hobby;
use Illuminate\Database\Seeder;

class HobbySeeder extends Seeder
{
    public function run(): void
    {
        $hobbies = [
            // Sports & Fitness
            ['name' => 'Running', 'category' => 'Sports'],
            ['name' => 'Cycling', 'category' => 'Sports'],
            ['name' => 'Swimming', 'category' => 'Sports'],
            ['name' => 'Yoga', 'category' => 'Fitness'],
            ['name' => 'Gym / Weightlifting', 'category' => 'Fitness'],
            ['name' => 'Hiking', 'category' => 'Outdoor'],
            ['name' => 'Surfing', 'category' => 'Sports'],
            ['name' => 'Skateboarding', 'category' => 'Sports'],
            ['name' => 'Climbing', 'category' => 'Sports'],
            ['name' => 'Martial Arts', 'category' => 'Sports'],
            ['name' => 'Dancing', 'category' => 'Arts'],
            ['name' => 'Soccer', 'category' => 'Sports'],
            ['name' => 'Volleyball', 'category' => 'Sports'],
            ['name' => 'Basketball', 'category' => 'Sports'],
            ['name' => 'Tennis', 'category' => 'Sports'],

            // Arts & Creativity
            ['name' => 'Photography', 'category' => 'Arts'],
            ['name' => 'Drawing / Painting', 'category' => 'Arts'],
            ['name' => 'Writing', 'category' => 'Arts'],
            ['name' => 'Music', 'category' => 'Arts'],
            ['name' => 'Singing', 'category' => 'Arts'],
            ['name' => 'Playing an Instrument', 'category' => 'Arts'],
            ['name' => 'Acting / Theater', 'category' => 'Arts'],
            ['name' => 'DIY / Crafts', 'category' => 'Arts'],

            // Food & Drink
            ['name' => 'Cooking', 'category' => 'Food'],
            ['name' => 'Baking', 'category' => 'Food'],
            ['name' => 'Wine Tasting', 'category' => 'Food'],
            ['name' => 'Coffee', 'category' => 'Food'],
            ['name' => 'Vegan / Plant-based', 'category' => 'Food'],

            // Travel & Adventure
            ['name' => 'Traveling', 'category' => 'Travel'],
            ['name' => 'Camping', 'category' => 'Outdoor'],
            ['name' => 'Road Trips', 'category' => 'Travel'],
            ['name' => 'Backpacking', 'category' => 'Travel'],
            ['name' => 'Beach', 'category' => 'Travel'],

            // Technology & Gaming
            ['name' => 'Gaming', 'category' => 'Tech'],
            ['name' => 'Programming', 'category' => 'Tech'],
            ['name' => 'Tech / Gadgets', 'category' => 'Tech'],
            ['name' => 'Anime / Manga', 'category' => 'Entertainment'],
            ['name' => 'Movies / Cinema', 'category' => 'Entertainment'],
            ['name' => 'Reading', 'category' => 'Entertainment'],
            ['name' => 'Board Games', 'category' => 'Entertainment'],

            // Nature & Animals
            ['name' => 'Pets / Animals', 'category' => 'Nature'],
            ['name' => 'Gardening', 'category' => 'Nature'],
            ['name' => 'Bird Watching', 'category' => 'Nature'],
            ['name' => 'Fishing', 'category' => 'Outdoor'],

            // Social & Lifestyle
            ['name' => 'Volunteering', 'category' => 'Social'],
            ['name' => 'Meditation', 'category' => 'Wellness'],
            ['name' => 'Astrology', 'category' => 'Lifestyle'],
            ['name' => 'Fashion', 'category' => 'Lifestyle'],
            ['name' => 'Makeup / Beauty', 'category' => 'Lifestyle'],
            ['name' => 'Karaoke', 'category' => 'Social'],
            ['name' => 'Nightlife / Parties', 'category' => 'Social'],
            ['name' => 'Podcasts', 'category' => 'Entertainment'],
        ];

        foreach ($hobbies as $hobby) {
            Hobby::firstOrCreate(
                ['name' => $hobby['name']],
                ['category' => $hobby['category'], 'active' => true]
            );
        }
    }
}