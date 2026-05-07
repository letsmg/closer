<?php

namespace Database\Factories;

use App\Models\Hobby;
use Illuminate\Database\Eloquent\Factories\Factory;

class HobbyFactory extends Factory
{
    protected $model = Hobby::class;

    public function definition()
    {
        $categories = ['intellectual', 'physical', 'artistic', 'social', 'lifestyle', 'entertainment', 'technology', 'nature'];
        $hobbies = [
            'Reading' => 'intellectual',
            'Writing' => 'intellectual',
            'Learning' => 'intellectual',
            'Chess' => 'intellectual',
            'Puzzles' => 'intellectual',
            'Football' => 'physical',
            'Basketball' => 'physical',
            'Swimming' => 'physical',
            'Running' => 'physical',
            'Yoga' => 'physical',
            'Gym' => 'physical',
            'Cycling' => 'physical',
            'Painting' => 'artistic',
            'Drawing' => 'artistic',
            'Music' => 'artistic',
            'Dancing' => 'artistic',
            'Photography' => 'artistic',
            'Cooking' => 'lifestyle',
            'Travel' => 'lifestyle',
            'Gardening' => 'lifestyle',
            'Fashion' => 'lifestyle',
            'Movies' => 'entertainment',
            'Gaming' => 'entertainment',
            'TV Shows' => 'entertainment',
            'Concerts' => 'entertainment',
            'Programming' => 'technology',
            'Robotics' => 'technology',
            'Hiking' => 'nature',
            'Camping' => 'nature',
            'Fishing' => 'nature',
        ];

        $hobbyName = $this->faker->unique()->randomElement(array_keys($hobbies));
        
        return [
            'name' => $hobbyName,
            'description' => $this->faker->sentence(10),
            'category' => $hobbies[$hobbyName],
            'icon' => $this->faker->randomElement(['heart', 'star', 'book', 'music', 'camera', 'gamepad', 'plane', 'tree']),
            'active' => true,
        ];
    }

    /**
     * Create a hobby from a specific category
     */
    public function category(string $category)
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }

    /**
     * Create an active hobby
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
        ]);
    }

    /**
     * Create an inactive hobby
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Create a hobby with specific name
     */
    public function name(string $name)
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Create an intellectual hobby
     */
    public function intellectual()
    {
        return $this->category('intellectual');
    }

    /**
     * Create a physical hobby
     */
    public function physical()
    {
        return $this->category('physical');
    }

    /**
     * Create an artistic hobby
     */
    public function artistic()
    {
        return $this->category('artistic');
    }

    /**
     * Create a social hobby
     */
    public function social()
    {
        return $this->category('social');
    }

    /**
     * Create a lifestyle hobby
     */
    public function lifestyle()
    {
        return $this->category('lifestyle');
    }

    /**
     * Create an entertainment hobby
     */
    public function entertainment()
    {
        return $this->category('entertainment');
    }

    /**
     * Create a technology hobby
     */
    public function technology()
    {
        return $this->category('technology');
    }

    /**
     * Create a nature hobby
     */
    public function nature()
    {
        return $this->category('nature');
    }

    /**
     * Create a hobby with specific icon
     */
    public function icon(string $icon)
    {
        return $this->state(fn (array $attributes) => [
            'icon' => $icon,
        ]);
    }

    /**
     * Create a hobby with specific description
     */
    public function description(string $description)
    {
        return $this->state(fn (array $attributes) => [
            'description' => $description,
        ]);
    }
}
