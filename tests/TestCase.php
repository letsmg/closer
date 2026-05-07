<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    /**
     * Setup test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set up any additional test configuration here
        // Don't run migrate:fresh here as RefreshDatabase trait handles it
    }

    /**
     * Reset test environment.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Create a user with authentication token
     */
    protected function createAuthenticatedUser($attributes = [])
    {
        $user = \App\Models\User::factory()->create($attributes);
        $token = $user->createToken('test-token')->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Create an admin user with authentication token
     */
    protected function createAdminUser($attributes = [])
    {
        return $this->createAuthenticatedUser(array_merge([
            'nivel_acesso' => 3, // Admin level
        ], $attributes));
    }

    /**
     * Create a premium user with authentication token
     */
    protected function createPremiumUser($attributes = [])
    {
        return $this->createAuthenticatedUser(array_merge([
            'nivel_acesso' => 2, // Premium level
        ], $attributes));
    }

    /**
     * Create a plus user with authentication token
     */
    protected function createPlusUser($attributes = [])
    {
        return $this->createAuthenticatedUser(array_merge([
            'nivel_acesso' => 1, // Plus level
        ], $attributes));
    }

    /**
     * Create a free user with authentication token
     */
    protected function createFreeUser($attributes = [])
    {
        return $this->createAuthenticatedUser(array_merge([
            'nivel_acesso' => 0, // Free level
        ], $attributes));
    }

    /**
     * Make an authenticated API request
     */
    protected function authenticatedRequest($method, $uri, $data = [], $token = null)
    {
        if ($token === null) {
            ['token' => $token] = $this->createAuthenticatedUser();
        }

        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json($method, $uri, $data);
    }

    /**
     * Make an authenticated API request as admin
     */
    protected function adminRequest($method, $uri, $data = [])
    {
        ['token' => $token] = $this->createAdminUser();
        
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json($method, $uri, $data);
    }

    /**
     * Assert database has table with expected structure
     */
    protected function assertTableHasColumns($table, array $columns)
    {
        foreach ($columns as $column) {
            $this->assertTrue(
                \Illuminate\Support\Facades\Schema::hasColumn($table, $column),
                "Table {$table} should have column {$column}"
            );
        }
    }

    /**
     * Assert database table structure matches expected
     */
    protected function assertTableStructure($table, array $expectedColumns)
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasTable($table),
            "Table {$table} should exist"
        );

        $this->assertTableHasColumns($table, $expectedColumns);
    }

    /**
     * Create test data with relationships
     */
    protected function createTestUserData()
    {
        $user = \App\Models\User::factory()->create();
        $profile = \App\Models\Profile::factory()->create(['user_id' => $user->id]);
        
        // Add some hobbies
        $hobbies = \App\Models\Hobby::factory()->count(3)->create();
        $profile->hobbies()->attach($hobbies->pluck('id'));

        // Add some languages
        $languages = \App\Models\Language::factory()->count(2)->create();
        foreach ($languages as $language) {
            $profile->languages()->attach($language->id, ['level' => 'fluent']);
        }

        // Add profile photos
        \App\Models\ProfilePhoto::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        return [
            'user' => $user,
            'profile' => $profile,
            'hobbies' => $hobbies,
            'languages' => $languages,
        ];
    }

    /**
     * Create a match between two users
     */
    protected function createMatch($user1, $user2)
    {
        // Create likes from both users
        \App\Models\LikeModel::factory()->create([
            'user_id' => $user1->id,
            'liked_user_id' => $user2->id,
            'is_like' => true,
        ]);

        \App\Models\LikeModel::factory()->create([
            'user_id' => $user2->id,
            'liked_user_id' => $user1->id,
            'is_like' => true,
        ]);

        // Create match record
        return \App\Models\UserMatch::factory()->create([
            'user_id' => $user1->id,
            'matched_user_id' => $user2->id,
        ]);
    }

    /**
     * Create conversation between matched users
     */
    protected function createConversation($match, $messageCount = 5)
    {
        $messages = [];
        
        for ($i = 0; $i < $messageCount; $i++) {
            $sender = $i % 2 === 0 ? $match->user_id : $match->matched_user_id;
            
            $messages[] = \App\Models\Message::factory()->create([
                'user_match_id' => $match->id,
                'sender_id' => $sender,
                'content' => "Test message " . ($i + 1),
                'read' => $i < $messageCount - 1, // Last message is unread
            ]);
        }

        return $messages;
    }

    /**
     * Assert JSON response has structure
     */
    protected function assertJsonStructure($response, array $structure)
    {
        $response->assertJsonStructure($structure);
        return $this;
    }

    /**
     * Assert JSON response has exact data
     */
    protected function assertJsonExact($response, array $data)
    {
        $response->assertJson($data);
        return $this;
    }

    /**
     * Assert validation errors
     */
    protected function assertValidationErrors($response, array $fields)
    {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors($fields);
        return $this;
    }

    /**
     * Assert unauthorized response
     */
    protected function assertUnauthorized($response)
    {
        $response->assertStatus(401);
        return $this;
    }

    /**
     * Assert forbidden response
     */
    protected function assertForbidden($response)
    {
        $response->assertStatus(403);
        return $this;
    }

    /**
     * Assert not found response
     */
    protected function assertNotFound($response)
    {
        $response->assertStatus(404);
        return $this;
    }

    /**
     * Assert successful response
     */
    protected function assertSuccess($response, $status = 200)
    {
        $response->assertStatus($status);
        return $this;
    }

    /**
     * Assert created response
     */
    protected function assertCreated($response)
    {
        $response->assertStatus(201);
        return $this;
    }

    /**
     * Assert no content response
     */
    protected function assertNoContent($response)
    {
        $response->assertStatus(204);
        return $this;
    }
}
