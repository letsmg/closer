<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hobby;

class HobbySeeder extends Seeder
{
    public function run(): void
    {
        $hobbies = [
            // ======================
            // Geek
            // ======================
            ['name' => 'Anime', 'category' => 'Geek'],
            ['name' => 'TV Series', 'category' => 'Geek'],
            ['name' => 'Movies', 'category' => 'Geek'],
            ['name' => 'Comics', 'category' => 'Geek'],
            ['name' => 'Cosplay', 'category' => 'Geek'],
            ['name' => 'Technology', 'category' => 'Geek'],

            // ======================
            // Science
            // ======================
            ['name' => 'Astronomy', 'category' => 'Science'],
            ['name' => 'Biology', 'category' => 'Science'],
            ['name' => 'Chemistry', 'category' => 'Science'],
            ['name' => 'Physics', 'category' => 'Science'],
            ['name' => 'Mathematics', 'category' => 'Science'],
            ['name' => 'Medicine', 'category' => 'Science'],

            // ======================
            // Sports
            // ======================
            ['name' => 'Football', 'category' => 'Sports'],
            ['name' => 'Basketball', 'category' => 'Sports'],
            ['name' => 'Tennis', 'category' => 'Sports'],
            ['name' => 'Swimming', 'category' => 'Sports'],
            ['name' => 'Running', 'category' => 'Sports'],
            ['name' => 'Cycling', 'category' => 'Sports'],
            ['name' => 'Yoga', 'category' => 'Sports'],
            ['name' => 'Gym', 'category' => 'Sports'],

            // ======================
            // Music
            // ======================
            ['name' => 'Rock', 'category' => 'Music'],
            ['name' => 'Pop', 'category' => 'Music'],
            ['name' => 'Jazz', 'category' => 'Music'],
            ['name' => 'Classical', 'category' => 'Music'],
            ['name' => 'Electronic', 'category' => 'Music'],
            ['name' => 'Hip Hop', 'category' => 'Music'],
            ['name' => 'R&B', 'category' => 'Music'],

            // ======================
            // Arts
            // ======================
            ['name' => 'Painting', 'category' => 'Arts'],
            ['name' => 'Drawing', 'category' => 'Arts'],
            ['name' => 'Sculpture', 'category' => 'Arts'],
            ['name' => 'Photography', 'category' => 'Arts'],
            ['name' => 'Digital Art', 'category' => 'Arts'],
            ['name' => 'Calligraphy', 'category' => 'Arts'],

            // ======================
            // Food & Cooking
            // ======================
            ['name' => 'Italian Cuisine', 'category' => 'Food'],
            ['name' => 'Japanese Cuisine', 'category' => 'Food'],
            ['name' => 'Mexican Cuisine', 'category' => 'Food'],
            ['name' => 'Indian Cuisine', 'category' => 'Food'],
            ['name' => 'Thai Cuisine', 'category' => 'Food'],
            ['name' => 'Baking', 'category' => 'Food'],
            ['name' => 'Grilling', 'category' => 'Food'],
            ['name' => 'Vegan Cooking', 'category' => 'Food'],

            // ======================
            // Travel
            // ======================
            ['name' => 'Backpacking', 'category' => 'Travel'],
            ['name' => 'Luxury Travel', 'category' => 'Travel'],
            ['name' => 'Adventure Travel', 'category' => 'Travel'],
            ['name' => 'Cultural Tourism', 'category' => 'Travel'],
            ['name' => 'Beach Destinations', 'category' => 'Travel'],
            ['name' => 'Mountain Hiking', 'category' => 'Travel'],
            ['name' => 'City Exploration', 'category' => 'Travel'],

            // ======================
            // Gaming
            // ======================
            ['name' => 'Video Games', 'category' => 'Gaming'],
            ['name' => 'Board Games', 'category' => 'Gaming'],
            ['name' => 'Tabletop RPG', 'category' => 'Gaming'],
            ['name' => 'Card Games', 'category' => 'Gaming'],
            ['name' => 'Mobile Gaming', 'category' => 'Gaming'],
            ['name' => 'PC Gaming', 'category' => 'Gaming'],
            ['name' => 'Console Gaming', 'category' => 'Gaming'],

            // ======================
            // Reading & Literature
            // ======================
            ['name' => 'Fiction', 'category' => 'Reading'],
            ['name' => 'Non-Fiction', 'category' => 'Reading'],
            ['name' => 'Poetry', 'category' => 'Reading'],
            ['name' => 'Mystery', 'category' => 'Reading'],
            ['name' => 'Romance', 'category' => 'Reading'],
            ['name' => 'Science Fiction', 'category' => 'Reading'],
            ['name' => 'Biography', 'category' => 'Reading'],

            // ======================
            // Fitness & Health
            // ======================
            ['name' => 'Weight Training', 'category' => 'Fitness'],
            ['name' => 'Cardio', 'category' => 'Fitness'],
            ['name' => 'Meditation', 'category' => 'Fitness'],
            ['name' => 'Pilates', 'category' => 'Fitness'],
            ['name' => 'CrossFit', 'category' => 'Fitness'],
            ['name' => 'Dance', 'category' => 'Fitness'],
            ['name' => 'Martial Arts', 'category' => 'Fitness'],

            // ======================
            // Fashion & Style
            // ======================
            ['name' => 'Street Fashion', 'category' => 'Fashion'],
            ['name' => 'Classic Fashion', 'category' => 'Fashion'],
            ['name' => 'Sustainable Fashion', 'category' => 'Fashion'],
            ['name' => 'Vintage Style', 'category' => 'Fashion'],
            ['name' => 'Minimalist Style', 'category' => 'Fashion'],
            ['name' => 'Accessories', 'category' => 'Fashion'],
            ['name' => 'Shoe Collection', 'category' => 'Fashion'],

            // ======================
            // Pets & Animals
            // ======================
            ['name' => 'Dogs', 'category' => 'Pets'],
            ['name' => 'Cats', 'category' => 'Pets'],
            ['name' => 'Birds', 'category' => 'Pets'],
            ['name' => 'Fish', 'category' => 'Pets'],
            ['name' => 'Small Animals', 'category' => 'Pets'],
            ['name' => 'Wildlife Photography', 'category' => 'Pets'],
            ['name' => 'Pet Training', 'category' => 'Pets'],

            // ======================
            // Technology & Innovation
            // ======================
            ['name' => 'Artificial Intelligence', 'category' => 'Technology'],
            ['name' => 'Blockchain', 'category' => 'Technology'],
            ['name' => 'Robotics', 'category' => 'Technology'],
            ['name' => 'Virtual Reality', 'category' => 'Technology'],
            ['name' => 'Programming', 'category' => 'Technology'],
            ['name' => 'Gadgets', 'category' => 'Technology'],
            ['name' => 'Smart Home', 'category' => 'Technology'],

            // ======================
            // Social & Community
            // ======================
            ['name' => 'Volunteering', 'category' => 'Social'],
            ['name' => 'Activism', 'category' => 'Social'],
            ['name' => 'Community Events', 'category' => 'Social'],
            ['name' => 'Networking', 'category' => 'Social'],
            ['name' => 'Public Speaking', 'category' => 'Social'],
            ['name' => 'Mentorship', 'category' => 'Social'],
            ['name' => 'Social Media', 'category' => 'Social'],
        ];

        foreach ($hobbies as $hobby) {
            Hobby::updateOrCreate(
                ['name' => $hobby['name'], 'category' => $hobby['category']],
                $hobby
            );
        }

        $this->command->info('Hobbies created successfully!');
    }
}
