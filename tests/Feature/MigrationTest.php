<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('users'));

        $expectedColumns = [
            'id',
            'name',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'created_at',
            'updated_at',
            'uuid',
            'ativo',
            'nivel_acesso',
            'reputacao',
            'ultima_interacao_at',
            'ultima_conversa_at',
            'assinatura_id',
            'premium_expira_em',
            'ultimo_ip',
            'ultimo_login_em',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('users', $column), "Column {$column} not found in users table");
        }
    }

    /** @test */
    public function profiles_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('profiles'));

        $expectedColumns = [
            'id',
            'user_id',
            'nickname',
            'birth_date',
            'gender',
            'gender_identity',
            'sexual_orientation',
            'purpose',
            'profession',
            'biography',
            'smoker',
            'drinker',
            'marital_status',
            'country_id',
            'state_id',
            'city_id',
            'visibility',
            'latitude',
            'longitude',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('profiles', $column), "Column {$column} not found in profiles table");
        }
    }

    /** @test */
    public function messages_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('messages'));

        $expectedColumns = [
            'id',
            'user_match_id',
            'sender_id',
            'content',
            'read',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('messages', $column), "Column {$column} not found in messages table");
        }
    }

    /** @test */
    public function likes_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('likes'));

        $expectedColumns = [
            'id',
            'user_id',
            'liked_user_id',
            'is_like',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('likes', $column), "Column {$column} not found in likes table");
        }
    }

    /** @test */
    public function hobbies_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('hobbies'));

        $expectedColumns = [
            'id',
            'name',
            'description',
            'category',
            'icon',
            'active',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('hobbies', $column), "Column {$column} not found in hobbies table");
        }
    }

    /** @test */
    public function profile_hobbies_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('profile_hobbies'));

        $expectedColumns = [
            'id',
            'profile_id',
            'hobby_id',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('profile_hobbies', $column), "Column {$column} not found in profile_hobbies table");
        }
    }

    /** @test */
    public function countries_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('countries'));

        $expectedColumns = [
            'id',
            'name',
            'code',
            'active',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('countries', $column), "Column {$column} not found in countries table");
        }
    }

    /** @test */
    public function states_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('states'));

        $expectedColumns = [
            'id',
            'country_id',
            'name',
            'code',
            'active',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('states', $column), "Column {$column} not found in states table");
        }
    }

    /** @test */
    public function cities_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('cities'));

        $expectedColumns = [
            'id',
            'state_id',
            'name',
            'active',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('cities', $column), "Column {$column} not found in cities table");
        }
    }

    /** @test */
    public function blocked_emails_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('blocked_emails'));

        $expectedColumns = [
            'id',
            'user_id',
            'banned_by',
            'email_hash',
            'reason',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('blocked_emails', $column), "Column {$column} not found in blocked_emails table");
        }
    }

    /** @test */
    public function user_matches_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('user_matches'));

        $expectedColumns = [
            'id',
            'user_id',
            'matched_user_id',
            'matched_at',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('user_matches', $column), "Column {$column} not found in user_matches table");
        }
    }

    /** @test */
    public function profile_photos_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('profile_photos'));

        $expectedColumns = [
            'id',
            'user_id',
            'photo_path',
            'is_primary',
            'order',
            'approved',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('profile_photos', $column), "Column {$column} not found in profile_photos table");
        }
    }

    /** @test */
    public function languages_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('languages'));

        $expectedColumns = [
            'id',
            'name',
            'code',
            'active',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('languages', $column), "Column {$column} not found in languages table");
        }
    }

    /** @test */
    public function profile_languages_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('profile_languages'));

        $expectedColumns = [
            'id',
            'profile_id',
            'language_id',
            'level',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('profile_languages', $column), "Column {$column} not found in profile_languages table");
        }
    }

    /** @test */
    public function reports_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('reports'));

        $expectedColumns = [
            'id',
            'reporter_id',
            'reported_id',
            'reason',
            'description',
            'status',
            'resolved_by',
            'resolved_at',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('reports', $column), "Column {$column} not found in reports table");
        }
    }

    /** @test */
    public function blocks_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('blocks'));

        $expectedColumns = [
            'id',
            'profile_id',
            'blocked_profile_id',
            'reason',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('blocks', $column), "Column {$column} not found in blocks table");
        }
    }

    /** @test */
    public function access_history_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('access_history'));

        $expectedColumns = [
            'id',
            'user_id',
            'ip_address',
            'user_agent',
            'login_at',
            'logout_at',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('access_history', $column), "Column {$column} not found in access_history table");
        }
    }

    /** @test */
    public function shorts_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('shorts'));

        $expectedColumns = [
            'id',
            'user_id',
            'video_path',
            'thumbnail_path',
            'caption',
            'duration',
            'views',
            'likes',
            'active',
            'approved',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('shorts', $column), "Column {$column} not found in shorts table");
        }
    }

    /** @test */
    public function refresh_tokens_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('refresh_tokens'));

        $expectedColumns = [
            'id',
            'user_id',
            'token',
            'expires_at',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('refresh_tokens', $column), "Column {$column} not found in refresh_tokens table");
        }
    }

    /** @test */
    public function personal_access_tokens_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('personal_access_tokens'));

        $expectedColumns = [
            'id',
            'tokenable_type',
            'tokenable_id',
            'name',
            'token',
            'abilities',
            'last_used_at',
            'expires_at',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('personal_access_tokens', $column), "Column {$column} not found in personal_access_tokens table");
        }
    }

    /** @test */
    public function cache_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('cache'));

        $expectedColumns = [
            'key',
            'value',
            'expiration',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('cache', $column), "Column {$column} not found in cache table");
        }
    }

    /** @test */
    public function jobs_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('jobs'));

        $expectedColumns = [
            'id',
            'queue',
            'payload',
            'attempts',
            'reserved_at',
            'available_at',
            'created_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('jobs', $column), "Column {$column} not found in jobs table");
        }
    }
}
