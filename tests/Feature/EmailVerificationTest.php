<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_verify_email_with_valid_code(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'status' => 'approved',
            'email_verified_at' => null,
            'email_verification_code' => '123456',
            'email_verification_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)->post('/email/verify', ['code' => '123456']);

        $response->assertRedirect();
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_already_verified_staff_user_is_redirected_to_staff_dashboard(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'approved',
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ]);

        $response = $this->actingAs($staff)->post('/email/verify', ['code' => '123456']);

        $response->assertRedirect(route('staff.dashboard'));
    }

    public function test_verified_email_auto_approves_user_without_manual_staff_action(): void
    {
        $user = User::factory()->create([
            'role' => 'researcher',
            'status' => 'pending',
            'email_verified_at' => null,
            'email_verification_code' => '654321',
            'email_verification_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)->post('/email/verify', ['code' => '654321']);

        $response->assertRedirect();
        $this->assertSame('approved', $user->fresh()->status);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
