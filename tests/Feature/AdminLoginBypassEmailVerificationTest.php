<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginBypassEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_login_without_email_verification(): void
    {
        $this->withoutMiddleware();

        $admin = User::factory()->create([
            'name' => 'System Admin',
            'role' => 'admin',
            'user_id' => 'ADM-001',
            'email' => 'admin@example.com',
            'password' => bcrypt('Password!123'),
            'email_verified_at' => null,
        ]);

        $response = $this->from('/login')->post(route('login.submit'), [
            'user_id' => 'ADM-001',
            'password' => 'Password!123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }
}
