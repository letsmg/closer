<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserMatchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_match_can_be_created_with_required_fields()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
        ]);

        $this->assertInstanceOf(UserMatch::class, $match);
        $this->assertEquals($user1->id, $match->user_one_id);
        $this->assertEquals($user2->id, $match->user_two_id);
        $this->assertFalse($match->is_favorite);
    }

    /** @test */
    public function user_match_has_user_relationships()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
        ]);

        $this->assertInstanceOf(User::class, $match->userOne);
        $this->assertEquals($user1->id, $match->userOne->id);

        $this->assertInstanceOf(User::class, $match->userTwo);
        $this->assertEquals($user2->id, $match->userTwo->id);
    }

    /** @test */
    public function user_match_can_be_marked_as_favorite()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
            'is_favorite' => true,
        ]);

        $this->assertTrue($match->is_favorite);
        $this->assertTrue($match->isFavorite());
    }

    /** @test */
    public function user_match_can_be_marked_as_not_favorite()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
            'is_favorite' => false,
        ]);

        $this->assertFalse($match->is_favorite);
        $this->assertFalse($match->isFavorite());
    }

    /** @test */
    public function user_match_can_be_marked_as_favorite_dynamically()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
            'is_favorite' => false,
        ]);

        $this->assertFalse($match->isFavorite());

        $match->markAsFavorite();
        $match->refresh();

        $this->assertTrue($match->isFavorite());
    }

    /** @test */
    public function user_match_can_be_unmarked_as_favorite_dynamically()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
            'is_favorite' => true,
        ]);

        $this->assertTrue($match->isFavorite());

        $match->markAsNotFavorite();
        $match->refresh();

        $this->assertFalse($match->isFavorite());
    }

    /** @test */
    public function user_match_favorites_scope_works()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $favoriteMatch = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
            'is_favorite' => true,
        ]);

        $regularMatch = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => User::factory()->create()->id,
            'is_favorite' => false,
        ]);

        $favorites = UserMatch::favorites()->get();
        $this->assertCount(1, $favorites);
        $this->assertTrue($favorites->contains($favoriteMatch));
        $this->assertFalse($favorites->contains($regularMatch));
    }

    /** @test */
    public function user_match_not_favorites_scope_works()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $favoriteMatch = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
            'is_favorite' => true,
        ]);

        $regularMatch = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => User::factory()->create()->id,
            'is_favorite' => false,
        ]);

        $notFavorites = UserMatch::notFavorites()->get();
        $this->assertCount(1, $notFavorites);
        $this->assertFalse($notFavorites->contains($favoriteMatch));
        $this->assertTrue($notFavorites->contains($regularMatch));
    }

    /** @test */
    public function user_match_fillable_fields_are_correct()
    {
        $fillable = [
            'user_one_id',
            'user_two_id',
            'is_favorite'
        ];

        $this->assertEquals($fillable, (new UserMatch)->getFillable());
    }

    /** @test */
    public function user_match_unique_constraint_works()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
        ]);
    }

    /** @test */
    public function user_match_factory_creates_favorite_match()
    {
        $match = UserMatch::factory()->favorite()->create();

        $this->assertTrue($match->is_favorite);
        $this->assertTrue($match->isFavorite());
    }

    /** @test */
    public function user_match_factory_creates_not_favorite_match()
    {
        $match = UserMatch::factory()->notFavorite()->create();

        $this->assertFalse($match->is_favorite);
        $this->assertFalse($match->isFavorite());
    }

    /** @test */
    public function user_match_factory_creates_between_users()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = UserMatch::factory()->between($user1, $user2)->create();

        $this->assertEquals($user1->id, $match->user_one_id);
        $this->assertEquals($user2->id, $match->user_two_id);
    }

    /** @test */
    public function user_match_casts_work_correctly()
    {
        $match = UserMatch::factory()->create([
            'is_favorite' => true,
        ]);

        $this->assertIsBool($match->is_favorite);
        $this->assertTrue($match->is_favorite);
    }

    /** @test */
    public function user_match_timestamps_are_set()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = UserMatch::factory()->create([
            'user_one_id' => $user1->id,
            'user_two_id' => $user2->id,
        ]);

        $this->assertNotNull($match->created_at);
        $this->assertNotNull($match->updated_at);
    }
}
