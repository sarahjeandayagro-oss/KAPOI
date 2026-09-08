<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntrancePortalSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_returns_matching_members_with_school(): void
    {
        Member::create([
            'barcode_id' => '100000001',
            'full_name' => 'Jane Doe',
            'school' => 'Panabo City National High School',
            'status' => 'active',
        ]);

        $response = $this->getJson(route('entrance.portal.search', ['query' => 'Jane']));

        $response->assertOk();
        $response->assertJsonFragment([
            'full_name' => 'Jane Doe',
            'school' => 'Panabo City National High School',
        ]);
    }

    public function test_search_excludes_unverified_users(): void
    {
        User::create([
            'name' => 'Unverified Jane',
            'email' => 'unverified@example.com',
            'user_id' => 'UNV-001',
            'barcode_id' => 'UNV-001',
            'school' => 'Panabo City National High School',
            'role' => 'student',
            'status' => 'active',
            'password' => bcrypt('Password!123'),
            'email_verified_at' => null,
        ]);

        User::create([
            'name' => 'Verified Jane',
            'email' => 'verified@example.com',
            'user_id' => 'VER-001',
            'barcode_id' => 'VER-001',
            'school' => 'Panabo City National High School',
            'role' => 'student',
            'status' => 'active',
            'password' => bcrypt('Password!123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->getJson(route('entrance.portal.search', ['query' => 'Jane']));

        $response->assertOk();
        $response->assertJsonFragment(['full_name' => 'Verified Jane']);
        $response->assertJsonMissing(['full_name' => 'Unverified Jane']);
    }
}
