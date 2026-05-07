<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Message;
use App\Models\UserMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function message_can_be_created_with_required_fields()
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
            'content' => 'Hello, how are you?',
            'read' => false,
        ]);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($match->id, $message->user_match_id);
        $this->assertEquals($sender->id, $message->sender_id);
        $this->assertEquals('Hello, how are you?', $message->content);
        $this->assertFalse($message->read);
    }

    /** @test */
    public function message_belongs_to_match()
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

        $this->assertInstanceOf(UserMatch::class, $message->match);
        $this->assertEquals($match->id, $message->match->id);
    }

    /** @test */
    public function message_belongs_to_sender()
    {
        $sender = User::factory()->create(['name' => 'John Doe']);
        $recipient = User::factory()->create();
        $match = UserMatch::factory()->create([
            'user_id' => $sender->id,
            'matched_user_id' => $recipient->id,
        ]);

        $message = Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $sender->id,
        ]);

        $this->assertInstanceOf(User::class, $message->sender);
        $this->assertEquals($sender->id, $message->sender->id);
        $this->assertEquals('John Doe', $message->sender->name);
    }

    /** @test */
    public function message_belongs_to_recipient_through_match()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create(['name' => 'Jane Doe']);
        $match = UserMatch::factory()->create([
            'user_id' => $sender->id,
            'matched_user_id' => $recipient->id,
        ]);

        $message = Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $sender->id,
        ]);

        $this->assertInstanceOf(User::class, $message->recipient);
        $this->assertEquals($recipient->id, $message->recipient->id);
        $this->assertEquals('Jane Doe', $message->recipient->name);
    }

    /** @test */
    public function message_read_status_methods_work()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $match = UserMatch::factory()->create([
            'user_id' => $sender->id,
            'matched_user_id' => $recipient->id,
        ]);

        $unreadMessage = Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $sender->id,
            'read' => false,
        ]);

        $readMessage = Message::factory()->create([
            'user_match_id' => $match->id,
            'sender_id' => $sender->id,
            'read' => true,
        ]);

        // Test isRead method
        $this->assertFalse($unreadMessage->isRead());
        $this->assertTrue($readMessage->isRead());

        // Test markAsRead method
        $unreadMessage->markAsRead();
        $unreadMessage->refresh();
        $this->assertTrue($unreadMessage->isRead());
    }

    /** @test */
    public function message_fillable_fields_are_correct()
    {
        $fillable = [
            'user_match_id',
            'sender_id',
            'content',
            'read'
        ];

        $this->assertEquals($fillable, (new Message)->getFillable());
    }

    /** @test */
    public function message_casts_work_correctly()
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
            'read' => true,
        ]);

        $this->assertIsBool($message->read);
        $this->assertTrue($message->read);
    }
}
