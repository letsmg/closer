<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\UserMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            'user_match_id' => UserMatch::factory(),
            'sender_id' => User::factory(),
            'content' => $this->faker->sentence($this->faker->numberBetween(5, 20)),
            'read' => false,
        ];
    }

    /**
     * Create a read message
     */
    public function read()
    {
        return $this->state(fn (array $attributes) => [
            'read' => true,
        ]);
    }

    /**
     * Create an unread message
     */
    public function unread()
    {
        return $this->state(fn (array $attributes) => [
            'read' => false,
        ]);
    }

    /**
     * Create a message for a specific match
     */
    public function forMatch(UserMatch $match)
    {
        return $this->state(fn (array $attributes) => [
            'user_match_id' => $match->id,
        ]);
    }

    /**
     * Create a message from a specific sender
     */
    public function from(User $sender)
    {
        return $this->state(fn (array $attributes) => [
            'sender_id' => $sender->id,
        ]);
    }

    /**
     * Create a message with specific content
     */
    public function content(string $content)
    {
        return $this->state(fn (array $attributes) => [
            'content' => $content,
        ]);
    }

    /**
     * Create a short message
     */
    public function short()
    {
        return $this->state(fn (array $attributes) => [
            'content' => $this->faker->word,
        ]);
    }

    /**
     * Create a long message
     */
    public function long()
    {
        return $this->state(fn (array $attributes) => [
            'content' => $this->faker->text(500),
        ]);
    }

    /**
     * Create a message with emoji
     */
    public function withEmoji()
    {
        return $this->state(fn (array $attributes) => [
            'content' => $this->faker->sentence() . ' ' . $this->faker->emoji,
        ]);
    }

    /**
     * Create a question message
     */
    public function question()
    {
        return $this->state(fn (array $attributes) => [
            'content' => $this->faker->sentence() . '?',
        ]);
    }

    /**
     * Create a greeting message
     */
    public function greeting()
    {
        $greetings = [
            'Hello! How are you?',
            'Hi there!',
            'Good morning!',
            'Hey, nice to meet you!',
            'Hi, how\'s your day going?',
        ];

        return $this->state(fn (array $attributes) => [
            'content' => $this->faker->randomElement($greetings),
        ]);
    }

    /**
     * Create a message for a specific match and sender
     */
    public function forMatchAndSender(UserMatch $match, User $sender)
    {
        return $this->state(fn (array $attributes) => [
            'user_match_id' => $match->id,
            'sender_id' => $sender->id,
        ]);
    }
}
