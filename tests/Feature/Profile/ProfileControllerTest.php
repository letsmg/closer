<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use App\Models\Profile;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Hobby;
use App\Models\ProfilePhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
    }

    /** @test */
    public function authenticated_user_can_view_their_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $photo = ProfilePhoto::factory()->create(['user_id' => $user->id]);
        
        $hobby = Hobby::factory()->create();
        $profile->hobbies()->attach($hobby->id);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'nickname',
                'birth_date',
                'gender',
                'biography',
                'profession',
                'country_id',
                'state_id',
                'city_id',
                'visibility',
                'latitude',
                'longitude',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'profile_photos'
                ],
                'hobbies'
            ]);

        $this->assertEquals($profile->id, $response->json('id'));
        $this->assertEquals($user->id, $response->json('user_id'));
    }

    /** @test */
    public function unauthenticated_user_cannot_view_profile()
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_update_their_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $city = City::factory()->create(['state_id' => $state->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $updateData = [
            'nickname' => 'new_nickname',
            'birth_date' => '1990-01-01',
            'gender' => 'female',
            'biography' => 'Updated biography',
            'profession' => 'Developer',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'visibility' => 'public',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/profile', $updateData);

        $response->assertStatus(200);

        $profile->refresh();
        $this->assertEquals('new_nickname', $profile->nickname);
        $this->assertEquals('female', $profile->gender);
        $this->assertEquals('Updated biography', $profile->biography);
        $this->assertEquals('Developer', $profile->profession);
        $this->assertEquals($country->id, $profile->country_id);
        $this->assertEquals($state->id, $profile->state_id);
        $this->assertEquals($city->id, $profile->city_id);
        $this->assertEquals('public', $profile->visibility);
    }

    /** @test */
    public function profile_update_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $invalidData = [
            'nickname' => '',
            'birth_date' => 'invalid-date',
            'gender' => 'invalid-gender',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/profile', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nickname', 'birth_date', 'gender']);
    }

    /** @test */
    public function user_can_update_profile_with_hobbies()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        
        $hobby1 = Hobby::factory()->create();
        $hobby2 = Hobby::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $updateData = [
            'nickname' => 'updated_nickname',
            'hobbies' => [$hobby1->id, $hobby2->id],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/profile', $updateData);

        $response->assertStatus(200);

        $profile->refresh();
        $this->assertCount(2, $profile->hobbies);
        $this->assertTrue($profile->hobbies->contains($hobby1));
        $this->assertTrue($profile->hobbies->contains($hobby2));
    }

    /** @test */
    public function user_can_update_profile_with_location_coordinates()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $updateData = [
            'latitude' => -23.5505,
            'longitude' => -46.6333,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/profile', $updateData);

        $response->assertStatus(200);

        $profile->refresh();
        $this->assertEquals(-23.5505, $profile->latitude);
        $this->assertEquals(-46.6333, $profile->longitude);
    }

    /** @test */
    public function user_cannot_update_another_users_profile()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $profile1 = Profile::factory()->create(['user_id' => $user1->id]);

        $token = $user2->createToken('auth_token')->plainTextToken;

        $updateData = [
            'nickname' => 'hacked_nickname',
        ];

        // Note: This test assumes the controller properly validates that the user
        // can only update their own profile. The actual implementation might vary.
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/profile', $updateData);

        // The response should either be 403 (forbidden) or 404 (not found)
        // depending on how the controller is implemented
        $this->assertContains($response->status(), [403, 404]);

        $profile1->refresh();
        $this->assertNotEquals('hacked_nickname', $profile1->nickname);
    }

    /** @test */
    public function profile_update_handles_file_upload()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $file = UploadedFile::fake()->image('profile.jpg');

        $updateData = [
            'nickname' => 'updated_with_photo',
            'profile_photo' => $file,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/profile', $updateData);

        // This test assumes the controller handles file uploads
        // The actual implementation might vary
        $response->assertStatus(200);

        $profile->refresh();
        $this->assertEquals('updated_with_photo', $profile->nickname);
    }

    /** @test */
    public function profile_update_validates_date_format()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $updateData = [
            'birth_date' => '1990-13-45', // Invalid date
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/profile', $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['birth_date']);
    }

    /** @test */
    public function profile_update_validates_location_exists()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $updateData = [
            'country_id' => 9999, // Non-existent country
            'state_id' => 9999,   // Non-existent state
            'city_id' => 9999,    // Non-existent city
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/profile', $updateData);

        // This test assumes the controller validates foreign key constraints
        // The actual validation rules might vary
        $response->assertStatus(422);
    }
}
