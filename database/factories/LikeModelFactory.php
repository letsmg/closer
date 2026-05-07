<?php

namespace Database\Factories;

use App\Models\LikeModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeModelFactory extends Factory
{
    protected $model = LikeModel::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'liked_user_id' => User::factory(),
            'is_like' => $this->faker->boolean(80), // 80% chance of being a like
        ];
    }

    /**
     * Create a like (positive)
     */
    public function like()
    {
        return $this->state(fn (array $attributes) => [
            'is_like' => true,
        ]);
    }

    /**
     * Create a dislike (negative)
     */
    public function dislike()
    {
        return $this->state(fn (array $attributes) => [
            'is_like' => false,
        ]);
    }

    /**
     * Create a like from a specific user
     */
    public function from(User $user)
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a like for a specific user
     */
    public function for(User $user)
    {
        return $this->state(fn (array $attributes) => [
            'liked_user_id' => $user->id,
        ]);
    }

    /**
     * Create a like between two specific users
     */
    public function between(User $liker, User $liked)
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $liker->id,
            'liked_user_id' => $liked->id,
        ]);
    }

    /**
     * Create a mutual like scenario (requires two calls)
     */
    public function mutual(User $user1, User $user2)
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user1->id,
            'liked_user_id' => $user2->id,
            'is_like' => true,
        ]);
    }

    /**
     * Create a like with specific timestamp
     */
    public function createdAt($dateTime)
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ]);
    }

    /**
     * Create a recent like
     */
    public function recent()
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    /**
     * Create an old like
     */
    public function old()
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-30 days', '-7 days'),
            'updated_at' => $this->faker->dateTimeBetween('-30 days', '-7 days'),
        ]);
    }
}
