<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResearcherRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_researcher_registration_can_be_submitted_without_user_id(): void
    {
        Storage::fake('public');

        $tempFile = tempnam(sys_get_temp_dir(), 'researcher-profile-');
        file_put_contents($tempFile, base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='));

        $file = new UploadedFile(
            $tempFile,
            'researcher-profile.gif',
            'image/gif',
            null,
            true
        );

        $response = $this->post(route('register.submit'), [
            'role' => 'researcher',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'birthdate' => '1990-01-01',
            'age' => 36,
            'school' => 'Davao del Norte State College',
            'gender' => 'Female',
            'profile_picture' => $file,
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'role' => 'researcher',
        ]);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotEmpty($user->user_id);
        $this->assertStringStartsWith('RES-', $user->user_id);
    }
}
