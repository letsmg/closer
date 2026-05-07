<?php

namespace Tests\Feature\Message;

use App\Models\User;
use App\Models\Message;
use App\Models\UserMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_send_message_to_match()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        
        $match = UserMatch::factory()->create([
            'user_id' => $sender->id,
            'matched_user_id' => $recipient->id,
        ]);

        $token = $sender->createToken('auth_token')->plainTextToken;

        $messageData = [
            'content' => 'Hello, how are you?',
            'user_match_id' => $match->id,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/messages', $messageData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_match_id',
                'sender_id',
                'content',
                'read',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('messages', [
            'user_match_id' => $match->id,
            'sender_id' => $sender->id,
            'content' => 'Hello, how are you?',
            'read' => false,
        ]);
    }

    /** @test */
    public function user_cannot_send_message_to_non_match()
    {
        $sender = User::factory()->create();
        $nonMatch = User::factory()->create();
        
        $token = $sender->createToken('auth_token')->plainTextToken;

        $messageData = [
            'content' => 'Hello!',
            'user_match_id' => 999, // Non-existent match
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/messages', $messageData);

        $response->assertStatus(422); // Or 404, depending on validation
    }

    /** @test */
    public function user_can_view_their_messages()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $match = UserMatch::factory()->create([
            'user_id' => $user1->id,
            'matched_user_id' => $user2->id,
        ]);

        $message1 = Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $user1->id,
            'content' => 'Hello from user1',
        ]);

        $message2 = Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $user2->id,
            'content' => 'Hello from user2',
        ]);

        $token = $user1->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/messages/{$match->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'user_match_id',
                    'sender_id',
                    'content',
                    'read',
                    'created_at',
                    'updated_at',
                    'sender' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]);
    }

    /** @test */
    public function user_cannot_view_messages_from_match_they_are_not_part_of()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        
        $match = UserMatch::factory()->create([
            'user_id' => $user2->id,
            'matched_user_id' => $user3->id,
        ]);

        Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $user2->id,
            'content' => 'Private message',
        ]);

        $token = $user1->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/messages/{$match->id}");

        $response->assertStatus(403); // Or 404, depending on implementation
    }

    /** @test */
    public function user_can_mark_message_as_read()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        
        $match = UserMatch::factory()->create([
            'user_id' => $sender->id,
            'matched_user_id' => $recipient->id,
        ]);

        $message = Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $sender->id,
            'read' => false,
        ]);

        $token = $recipient->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson("/api/messages/{$message->id}/read");

        $response->assertStatus(200);

        $message->refresh();
        $this->assertTrue($message->read);
    }

    /** @test */
    public function user_can_only_mark_messages_sent_to_them_as_read()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $match = UserMatch::factory()->create([
            'user_id' => $user1->id,
            'matched_user_id' => $user2->id,
        ]);

        $message = Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $user1->id,
            'read' => false,
        ]);

        $token = $user1->createToken('auth_token')->plainTextToken; // Sender trying to mark as read

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson("/api/messages/{$message->id}/read");

        $response->assertStatus(403); // Should not allow sender to mark as read

        $message->refresh();
        $this->assertFalse($message->read);
    }

    /** @test */
    public function message_validation_works()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        
        $match = UserMatch::factory()->create([
            'user_id' => $sender->id,
            'matched_user_id' => $recipient->id,
        ]);

        $token = $sender->createToken('auth_token')->plainTextToken;

        // Test empty content
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/messages', [
            'content' => '',
            'user_match_id' => $match->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);

        // Test missing content
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/messages', [
            'user_match_id' => $match->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    /** @test */
    public function message_content_length_is_limited()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        
        $match = UserMatch::factory()->create([
            'user_id' => $sender->id,
            'matched_user_id' => $recipient->id,
        ]);

        $token = $sender->createToken('auth_token')->plainTextToken;

        $longContent = str_repeat('a', 1001); // Assuming 1000 character limit

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/messages', [
            'content' => $longContent,
            'user_match_id' => $match->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_message_endpoints()
    {
        $response = $this->postJson('/api/messages', [
            'content' => 'Hello',
            'user_match_id' => 1,
        ]);

        $response->assertStatus(401);

        $response = $this->getJson('/api/messages/1');
        $response->assertStatus(401);

        $response = $this->patchJson('/api/messages/1/read');
        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_delete_the_own_messages()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        
        $match = UserMatch::factory()->create([
            'user_id' => $sender->id,
            'matched_user_id' => $recipient->id,
        ]);

        $message = Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $sender->id,
        ]);

        $token = $sender->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
    }

    /** @test */
    public function user_cannot_delete_others_messages()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        
        $match = UserMatch::factory()->create([
            'user_id' => $sender->id,
            'matched_user_id' => $recipient->id,
        ]);

        $message = Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $sender->id,
        ]);

        $token = $recipient->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('messages', ['id' => $message->id]);
    }
}
