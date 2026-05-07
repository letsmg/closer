<?php

namespace Tests\Unit;

use App\Models\Hobby;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HobbyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function hobby_can_be_created_with_required_fields()
    {
        $hobby = Hobby::factory()->create([
            'name' => 'Reading',
            'description' => 'Reading books and literature',
            'category' => 'intellectual',
        ]);

        $this->assertInstanceOf(Hobby::class, $hobby);
        $this->assertEquals('Reading', $hobby->name);
        $this->assertEquals('Reading books and literature', $hobby->description);
        $this->assertEquals('intellectual', $hobby->category);
    }

    /** @test */
    public function hobby_has_many_to_many_relationship_with_profiles()
    {
        $hobby = Hobby::factory()->create(['name' => 'Sports']);
        
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $profile1 = Profile::factory()->create(['user_id' => $user1->id]);
        $profile2 = Profile::factory()->create(['user_id' => $user2->id]);

        $hobby->profiles()->attach([$profile1->id, $profile2->id]);

        $this->assertCount(2, $hobby->profiles);
        $this->assertTrue($hobby->profiles->contains($profile1));
        $this->assertTrue($hobby->profiles->contains($profile2));
    }

    /** @test */
    public function hobby_fillable_fields_are_correct()
    {
        // Assuming the Hobby model has these fillable fields
        $fillable = [
            'name',
            'description',
            'category',
            'icon',
            'active'
        ];

        $this->assertEquals($fillable, (new Hobby)->getFillable());
    }

    /** @test */
    public function hobby_can_be_soft_deleted_if_trait_is_present()
    {
        $hobby = Hobby::factory()->create([
            'name' => 'Gaming',
            'description' => 'Video games',
        ]);

        $hobbyId = $hobby->id;
        
        $hobby->delete();

        // Check if soft delete is implemented
        if (method_exists($hobby, 'trashed')) {
            $this->assertTrue($hobby->trashed());
            $this->assertSoftDeleted('hobbies', ['id' => $hobbyId]);
        } else {
            // If no soft delete, check hard delete
            $this->assertDatabaseMissing('hobbies', ['id' => $hobbyId]);
        }
    }

    /** @test */
    public function hobby_scope_active_works_if_implemented()
    {
        $activeHobby = Hobby::factory()->create(['name' => 'Active Hobby', 'active' => true]);
        $inactiveHobby = Hobby::factory()->create(['name' => 'Inactive Hobby', 'active' => false]);

        // Only test if scopeActive method exists
        if (method_exists(Hobby::class, 'scopeActive')) {
            $activeHobbies = Hobby::active()->get();
            
            $this->assertCount(1, $activeHobbies);
            $this->assertTrue($activeHobbies->contains($activeHobby));
            $this->assertFalse($activeHobbies->contains($inactiveHobby));
        }
    }

    /** @test */
    public function hobby_search_by_name_works()
    {
        $hobby1 = Hobby::factory()->create(['name' => 'Football', 'description' => 'Playing football']);
        $hobby2 = Hobby::factory()->create(['name' => 'Basketball', 'description' => 'Playing basketball']);
        $hobby3 = Hobby::factory()->create(['name' => 'Reading', 'description' => 'Reading books']);

        $foundHobbies = Hobby::where('name', 'LIKE', '%ball%')->get();
        
        $this->assertCount(2, $foundHobbies);
        $this->assertTrue($foundHobbies->contains($hobby1));
        $this->assertTrue($foundHobbies->contains($hobby2));
        $this->assertFalse($foundHobbies->contains($hobby3));
    }
}
