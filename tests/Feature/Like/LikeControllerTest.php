<?php

namespace Tests\Feature\Like;

use App\Models\User;
use App\Models\LikeModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_like_another_user()
    {
        $liker = User::factory()->create();
        $likedUser = User::factory()->create();

        $token = $liker->createToken('auth_token')->plainTextToken;

        $likeData = [
            'liked_user_id' => $likedUser->id,
            'is_like' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/likes', $likeData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'liked_user_id',
                'is_like',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $liker->id,
            'liked_user_id' => $likedUser->id,
            'is_like' => true,
        ]);
    }

    /** @test */
    public function user_can_dislike_another_user()
    {
        $liker = User::factory()->create();
        $dislikedUser = User::factory()->create();

        $token = $liker->createToken('auth_token')->plainTextToken;

        $likeData = [
            'liked_user_id' => $dislikedUser->id,
            'is_like' => false,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/likes', $likeData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('likes', [
            'user_id' => $liker->id,
            'liked_user_id' => $dislikedUser->id,
            'is_like' => false,
        ]);
    }

    /** @test */
    public function user_cannot_like_themselves()
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $likeData = [
            'liked_user_id' => $user->id,
            'is_like' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/likes', $likeData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['liked_user_id']);
    }

    /** @test */
    public function user_cannot_like_nonexistent_user()
    {
        $liker = User::factory()->create();

        $token = $liker->createToken('auth_token')->plainTextToken;

        $likeData = [
            'liked_user_id' => 9999,
            'is_like' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/likes', $likeData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['liked_user_id']);
    }

    /** @test */
    public function user_cannot_like_same_user_twice()
    {
        $liker = User::factory()->create();
        $likedUser = User::factory()->create();

        LikeModel::factory()->create([
            'user_id' => $liker->id,
            'liked_user_id' => $likedUser->id,
            'is_like' => true,
        ]);

        $token = $liker->createToken('auth_token')->plainTextToken;

        $likeData = [
            'liked_user_id' => $likedUser->id,
            'is_like' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/likes', $likeData);

        $response->assertStatus(422);
    }

    /** @test */
    public function user_can_view_their_likes()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        LikeModel::factory()->create([
            'user_id' => $user->id,
            'liked_user_id' => $user1->id,
            'is_like' => true,
        ]);

        LikeModel::factory()->create([
            'user_id' => $user->id,
            'liked_user_id' => $user2->id,
            'is_like' => false,
        ]);

        LikeModel::factory()->create([
            'user_id' => $user3->id,
            'liked_user_id' => $user->id,
            'is_like' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/likes');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'user_id',
                    'liked_user_id',
                    'is_like',
                    'created_at',
                    'updated_at',
                    'likedUser' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]);
    }

    /** @test */
    public function user_can_view_people_who_liked_them()
    {
        $user = User::factory()->create();
        $liker1 = User::factory()->create();
        $liker2 = User::factory()->create();

        LikeModel::factory()->create([
            'user_id' => $liker1->id,
            'liked_user_id' => $user->id,
            'is_like' => true,
        ]);

        LikeModel::factory()->create([
            'user_id' => $liker2->id,
            'liked_user_id' => $user->id,
            'is_like' => true,
        ]);

        LikeModel::factory()->create([
            'user_id' => $user->id,
            'liked_user_id' => $liker1->id,
            'is_like' => false,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/likes/received');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'user_id',
                    'liked_user_id',
                    'is_like',
                    'created_at',
                    'updated_at',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]);
    }

    /** @test */
    public function user_can_update_existing_like()
    {
        $liker = User::factory()->create();
        $likedUser = User::factory()->create();

        $like = LikeModel::factory()->create([
            'user_id' => $liker->id,
            'liked_user_id' => $likedUser->id,
            'is_like' => false,
        ]);

        $token = $liker->createToken('auth_token')->plainTextToken;

        $updateData = [
            'is_like' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/likes/{$like->id}", $updateData);

        $response->assertStatus(200);

        $like->refresh();
        $this->assertTrue($like->is_like);
    }

    /** @test */
    public function user_can_remove_like()
    {
        $liker = User::factory()->create();
        $likedUser = User::factory()->create();

        $like = LikeModel::factory()->create([
            'user_id' => $liker->id,
            'liked_user_id' => $likedUser->id,
            'is_like' => true,
        ]);

        $token = $liker->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/likes/{$like->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('likes', ['id' => $like->id]);
    }

    /** @test */
    public function user_cannot_modify_others_likes()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $like = LikeModel::factory()->create([
            'user_id' => $user2->id,
            'liked_user_id' => $user3->id,
            'is_like' => true,
        ]);

        $token = $user1->createToken('auth_token')->plainTextToken;

        $updateData = [
            'is_like' => false,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/likes/{$like->id}", $updateData);

        $response->assertStatus(403);

        $like->refresh();
        $this->assertTrue($like->is_like);
    }

    /** @test */
    public function like_validation_works()
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        // Test missing liked_user_id
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/likes', [
            'is_like' => true,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['liked_user_id']);

        // Test missing is_like
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/likes', [
            'liked_user_id' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_like']);

        // Test invalid is_like type
        $otherUser = User::factory()->create();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/likes', [
            'liked_user_id' => $otherUser->id,
            'is_like' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_like']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_like_endpoints()
    {
        $response = $this->postJson('/api/likes', [
            'liked_user_id' => 1,
            'is_like' => true,
        ]);

        $response->assertStatus(401);

        $response = $this->getJson('/api/likes');
        $response->assertStatus(401);

        $response = $this->getJson('/api/likes/received');
        $response->assertStatus(401);

        $response = $this->putJson('/api/likes/1', ['is_like' => false]);
        $response->assertStatus(401);

        $response = $this->deleteJson('/api/likes/1');
        $response->assertStatus(401);
    }

    /** @test */
    public function mutual_like_creates_match()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // First user likes second user
        LikeModel::factory()->create([
            'user_id' => $user1->id,
            'liked_user_id' => $user2->id,
            'is_like' => true,
        ]);

        $token = $user2->createToken('auth_token')->plainTextToken;

        // Second user likes first user back
        $likeData = [
            'liked_user_id' => $user1->id,
            'is_like' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/likes', $likeData);

        $response->assertStatus(201);

        // Check if a match was created (assuming this logic exists)
        $this->assertDatabaseHas('user_matches', function ($query) use ($user1, $user2) {
            return $query->where(function ($q) use ($user1, $user2) {
                $q->where('user_id', $user1->id)
                  ->where('matched_user_id', $user2->id);
            })->orWhere(function ($q) use ($user1, $user2) {
                $q->where('user_id', $user2->id)
                  ->where('matched_user_id', $user1->id);
            });
        });
    }
}
