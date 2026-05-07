<?php

namespace Database\Factories;

use App\Models\UserMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserMatchFactory extends Factory
{
    protected $model = UserMatch::class;

    public function definition()
    {
        return [
            'user_one_id' => User::factory(),
            'user_two_id' => User::factory(),
            'is_favorite' => false,
        ];
    }

    /**
     * Create a match between two specific users
     */
    public function between(User $user1, User $user2)
    {
        return $this->state(fn (array $attributes) => [
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
        ]);
    }

    /**
     * Create a favorite match
     */
    public function favorite()
    {
        return $this->state(fn (array $attributes) => [
            'is_favorite' => true,
        ]);
    }

    /**
     * Create a non-favorite match
     */
    public function notFavorite()
    {
        return $this->state(fn (array $attributes) => [
            'is_favorite' => false,
        ]);
    }
}
