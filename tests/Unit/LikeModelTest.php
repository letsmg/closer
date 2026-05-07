<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\LikeModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function like_can_be_created_with_required_fields()
    {
        $user = User::factory()->create();
        $likedUser = User::factory()->create();

        $like = LikeModel::factory()->create([
            'user_id' => $user->id,
            'liked_user_id' => $likedUser->id,
            'is_like' => true,
        ]);

        $this->assertInstanceOf(LikeModel::class, $like);
        $this->assertEquals($user->id, $like->user_id);
        $this->assertEquals($likedUser->id, $like->liked_user_id);
        $this->assertTrue($like->is_like);
    }

    /** @test */
    public function like_can_be_dislike()
    {
        $user = User::factory()->create();
        $likedUser = User::factory()->create();

        $dislike = LikeModel::factory()->create([
            'user_id' => $user->id,
            'liked_user_id' => $likedUser->id,
            'is_like' => false,
        ]);

        $this->assertInstanceOf(LikeModel::class, $dislike);
        $this->assertFalse($dislike->is_like);
    }

    /** @test */
    public function like_belongs_to_user()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $likedUser = User::factory()->create();

        $like = LikeModel::factory()->create([
            'user_id' => $user->id,
            'liked_user_id' => $likedUser->id,
        ]);

        $this->assertInstanceOf(User::class, $like->user);
        $this->assertEquals($user->id, $like->user->id);
        $this->assertEquals('John Doe', $like->user->name);
    }

    /** @test */
    public function like_belongs_to_liked_user()
    {
        $user = User::factory()->create();
        $likedUser = User::factory()->create(['name' => 'Jane Doe']);

        $like = LikeModel::factory()->create([
            'user_id' => $user->id,
            'liked_user_id' => $likedUser->id,
        ]);

        $this->assertInstanceOf(User::class, $like->likedUser);
        $this->assertEquals($likedUser->id, $like->likedUser->id);
        $this->assertEquals('Jane Doe', $like->likedUser->name);
    }

    /** @test */
    public function like_fillable_fields_are_correct()
    {
        $fillable = [
            'user_id',
            'liked_user_id',
            'is_like'
        ];

        $this->assertEquals($fillable, (new LikeModel)->getFillable());
    }

    /** @test */
    public function like_casts_work_correctly()
    {
        $user = User::factory()->create();
        $likedUser = User::factory()->create();

        $like = LikeModel::factory()->create([
            'user_id' => $user->id,
            'liked_user_id' => $likedUser->id,
            'is_like' => true,
        ]);

        $this->assertIsBool($like->is_like);
        $this->assertTrue($like->is_like);
    }

    /** @test */
    public function unique_constraint_prevents_duplicate_likes()
    {
        $user = User::factory()->create();
        $likedUser = User::factory()->create();

        LikeModel::factory()->create([
            'user_id' => $user->id,
            'liked_user_id' => $likedUser->id,
            'is_like' => true,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        LikeModel::factory()->create([
            'user_id' => $user->id,
            'liked_user_id' => $likedUser->id,
            'is_like' => false,
        ]);
    }
}
