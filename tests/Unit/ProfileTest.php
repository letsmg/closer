<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Profile;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Hobby;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_can_be_created_with_required_fields()
    {
        $user = User::factory()->create();
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $city = City::factory()->create(['state_id' => $state->id]);

        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'nickname' => 'john_doe',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals($user->id, $profile->user_id);
        $this->assertEquals('john_doe', $profile->nickname);
        $this->assertEquals('male', $profile->gender);
    }

    /** @test */
    public function profile_has_user_relationship()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $profile->user);
        $this->assertEquals($user->id, $profile->user->id);
    }

    /** @test */
    public function profile_has_location_relationships()
    {
        $user = User::factory()->create();
        $country = Country::factory()->create(['name' => 'Brazil']);
        $state = State::factory()->create(['country_id' => $country->id, 'name' => 'São Paulo']);
        $city = City::factory()->create(['state_id' => $state->id, 'name' => 'São Paulo']);

        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $this->assertInstanceOf(Country::class, $profile->country);
        $this->assertEquals('Brazil', $profile->country->name);

        $this->assertInstanceOf(State::class, $profile->state);
        $this->assertEquals('São Paulo', $profile->state->name);

        $this->assertInstanceOf(City::class, $profile->city);
        $this->assertEquals('São Paulo', $profile->city->name);
    }

    /** @test */
    public function profile_has_many_to_many_relationships()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $language1 = Language::factory()->create(['name' => 'English']);
        $language2 = Language::factory()->create(['name' => 'Portuguese']);

        $hobby1 = Hobby::factory()->create(['name' => 'Reading']);
        $hobby2 = Hobby::factory()->create(['name' => 'Sports']);

        $profile->languages()->attach([$language1->id, $language2->id], ['level' => 'fluent']);
        $profile->hobbies()->attach([$hobby1->id, $hobby2->id]);

        $this->assertCount(2, $profile->languages);
        $this->assertTrue($profile->languages->contains($language1));
        $this->assertTrue($profile->languages->contains($language2));

        $this->assertCount(2, $profile->hobbies);
        $this->assertTrue($profile->hobbies->contains($hobby1));
        $this->assertTrue($profile->hobbies->contains($hobby2));
    }

    /** @test */
    public function profile_scope_visible_works()
    {
        $user = User::factory()->create();

        $publicProfile = Profile::factory()->create([
            'user_id' => $user->id,
            'visibility' => 'public'
        ]);

        $hiddenProfile = Profile::factory()->create([
            'user_id' => $user->id,
            'visibility' => 'hidden'
        ]);

        $visibleProfiles = Profile::visible()->get();

        $this->assertCount(1, $visibleProfiles);
        $this->assertTrue($visibleProfiles->contains($publicProfile));
        $this->assertFalse($visibleProfiles->contains($hiddenProfile));
    }

    /** @test */
    public function profile_age_accessor_works()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'birth_date' => now()->subYears(25)
        ]);

        $this->assertEquals(25, $profile->age);
    }

    /** @test */
    public function profile_full_name_accessor_works()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->assertEquals('John Doe', $profile->full_name);
    }

    /** @test */
    public function profile_visibility_methods_work()
    {
        $user = User::factory()->create();

        $publicProfile = Profile::factory()->create([
            'user_id' => $user->id,
            'visibility' => 'public'
        ]);

        $hiddenProfile = Profile::factory()->create([
            'user_id' => $user->id,
            'visibility' => 'hidden'
        ]);

        $this->assertTrue($publicProfile->isVisible());
        $this->assertFalse($publicProfile->isBlocked());

        $this->assertFalse($hiddenProfile->isVisible());
        $this->assertTrue($hiddenProfile->isBlocked());
    }

    /** @test */
    public function profile_completion_percentage_works()
    {
        $user = User::factory()->create();

        $emptyProfile = Profile::factory()->create([
            'user_id' => $user->id,
            'nickname' => null,
            'birth_date' => null,
            'gender' => null,
            'biography' => null,
            'profession' => null
        ]);

        $partialProfile = Profile::factory()->create([
            'user_id' => $user->id,
            'nickname' => 'john_doe',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'biography' => null,
            'profession' => null
        ]);

        $completeProfile = Profile::factory()->create([
            'user_id' => $user->id,
            'nickname' => 'jane_doe',
            'birth_date' => '1990-01-01',
            'gender' => 'female',
            'biography' => 'Software developer',
            'profession' => 'Developer'
        ]);

        $this->assertEquals(0, $emptyProfile->getCompletionPercentage());
        $this->assertEquals(60, $partialProfile->getCompletionPercentage()); // 3/5 fields
        $this->assertEquals(100, $completeProfile->getCompletionPercentage()); // 5/5 fields
    }

    /** @test */
    public function profile_can_be_viewed_by_owner()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'visibility' => 'hidden'
        ]);

        $this->assertTrue($profile->canBeViewedBy($user));
    }

    /** @test */
    public function profile_cannot_be_viewed_by_unauthenticated_user()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'visibility' => 'public'
        ]);

        $this->assertFalse($profile->canBeViewedBy(null));
    }

    /** @test */
    public function profile_fillable_fields_are_correct()
    {
        $fillable = [
            'user_id', 'nickname', 'birth_date', 'gender', 'gender_identity',
            'sexual_orientation', 'purpose', 'profession', 'biography', 'smoker',
            'drinker', 'marital_status', 'country_id', 'state_id', 'city_id',
            'visibility', 'latitude', 'longitude'
        ];

        $this->assertEquals($fillable, (new Profile)->getFillable());
    }
}
