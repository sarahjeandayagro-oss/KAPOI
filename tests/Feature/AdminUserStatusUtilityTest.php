<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserStatusUtilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_mark_a_user_as_retired_or_transferred(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $user = User::factory()->create([
            'role' => 'student',
            'status' => 'approved',
            'user_id' => 'STU-1001',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.status'), [
            'lookup' => $user->user_id,
            'status' => 'retired',
        ]);

        $response->assertRedirect();
        $this->assertSame('retired', $user->fresh()->status);
    }

    public function test_admin_can_create_and_reset_user_account_credentials(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $storeResponse = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Rosa Staff',
            'email' => 'rosa.staff@example.com',
            'user_id' => 'STA-9001',
            'role' => 'staff',
            'status' => 'approved',
            'password' => 'StaffPass!23',
            'password_confirmation' => 'StaffPass!23',
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'rosa.staff@example.com', 'role' => 'staff']);

        $user = User::where('email', 'rosa.staff@example.com')->firstOrFail();

        $resetResponse = $this->actingAs($admin)->post(route('admin.users.reset-password', $user->id), [
            'user_id' => 'STA-9010',
            'password' => 'NewStaffPass!45',
            'password_confirmation' => 'NewStaffPass!45',
        ]);

        $resetResponse->assertRedirect();
        $user->refresh();
        $this->assertSame('STA-9010', $user->user_id);
        $this->assertTrue(password_verify('NewStaffPass!45', $user->password));
    }

    public function test_same_person_can_register_same_email_with_different_role(): void
    {
        User::factory()->create([
            'name' => 'Maria Cruz',
            'email' => 'maria@example.com',
            'user_id' => 'RES-1001',
            'role' => 'researcher',
            'status' => 'approved',
        ]);

        $pngPath = tempnam(sys_get_temp_dir(), 'lib-reg-');
        file_put_contents($pngPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAF' . 'c0QxAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJ0UkG' . 'AAAAAAgIY3WAQAAABJRU5ErkJggg=='));

        $response = $this->post(route('register.submit'), [
            'name' => 'Maria Cruz',
            'email' => 'maria@example.com',
            'password' => 'StrongPass!22',
            'password_confirmation' => 'StrongPass!22',
            'role' => 'student',
            'birthdate' => '2000-01-15',
            'age' => 26,
            'school' => 'Davao del Norte State College',
            'gender' => 'Female',
            'user_id' => 'STU-9001',
            'profile_picture' => new \Illuminate\Http\UploadedFile($pngPath, 'profile.png', 'image/png', null, true),
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => 'maria@example.com',
            'role' => 'student',
            'user_id' => 'STU-9001',
        ]);
    }
}
