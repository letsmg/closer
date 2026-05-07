<?php

namespace Database\Factories;

use App\Models\ProfilePhoto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfilePhotoFactory extends Factory
{
    protected $model = ProfilePhoto::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'photo_path' => 'profile_photos/' . $this->faker->uuid() . '.jpg',
            'is_primary' => false,
            'order' => $this->faker->numberBetween(1, 6),
            'approved' => true,
        ];
    }

    /**
     * Create a primary photo
     */
    public function primary()
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'order' => 1,
        ]);
    }

    /**
     * Create a secondary photo
     */
    public function secondary()
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => false,
            'order' => $this->faker->numberBetween(2, 6),
        ]);
    }

    /**
     * Create an approved photo
     */
    public function approved()
    {
        return $this->state(fn (array $attributes) => [
            'approved' => true,
        ]);
    }

    /**
     * Create a pending photo
     */
    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'approved' => false,
        ]);
    }

    /**
     * Create a rejected photo
     */
    public function rejected()
    {
        return $this->state(fn (array $attributes) => [
            'approved' => false,
        ]);
    }

    /**
     * Create a photo for a specific user
     */
    public function forUser(User $user)
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a photo with specific path
     */
    public function path(string $path)
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => $path,
        ]);
    }

    /**
     * Create a photo with specific order
     */
    public function order(int $order)
    {
        return $this->state(fn (array $attributes) => [
            'order' => $order,
        ]);
    }

    /**
     * Create a photo with specific order and make it primary if order is 1
     */
    public function orderWithPrimary(int $order)
    {
        return $this->state(fn (array $attributes) => [
            'order' => $order,
            'is_primary' => $order === 1,
        ]);
    }

    /**
     * Create a photo with fake image path
     */
    public function fakeImage()
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => 'profile_photos/fake_' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Create a photo with specific extension
     */
    public function extension(string $extension)
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => 'profile_photos/' . $this->faker->uuid() . '.' . $extension,
        ]);
    }

    /**
     * Create a JPEG photo
     */
    public function jpeg()
    {
        return $this->extension('jpg');
    }

    /**
     * Create a PNG photo
     */
    public function png()
    {
        return $this->extension('png');
    }

    /**
     * Create a WebP photo
     */
    public function webp()
    {
        return $this->extension('webp');
    }

    /**
     * Create a photo with timestamp in filename
     */
    public function withTimestamp()
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => 'profile_photos/' . now()->timestamp . '_' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Create a photo with user ID in filename
     */
    public function withUserId(User $user)
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'photo_path' => 'profile_photos/user_' . $user->id . '_' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Create the first photo (order 1, primary)
     */
    public function first()
    {
        return $this->state(fn (array $attributes) => [
            'order' => 1,
            'is_primary' => true,
        ]);
    }

    /**
     * Create the second photo (order 2)
     */
    public function second()
    {
        return $this->state(fn (array $attributes) => [
            'order' => 2,
            'is_primary' => false,
        ]);
    }

    /**
     * Create the third photo (order 3)
     */
    public function third()
    {
        return $this->state(fn (array $attributes) => [
            'order' => 3,
            'is_primary' => false,
        ]);
    }

    /**
     * Create a photo with specific dimensions in filename
     */
    public function dimensions(int $width, int $height)
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => "profile_photos/{$width}x{$height}_" . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Create a thumbnail photo
     */
    public function thumbnail()
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => 'profile_photos/thumbnails/' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Create a full-size photo
     */
    public function fullSize()
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => 'profile_photos/full/' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Create a compressed photo
     */
    public function compressed()
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => 'profile_photos/compressed/' . $this->faker->uuid() . '.jpg',
        ]);
    }
}
