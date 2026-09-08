<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RequestHistoryViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_dashboard_shows_request_history_section(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'approved',
            'user_id' => 'STU-9001',
        ]);

        DB::table('borrowing_transactions')->insert([
            'user_id' => $student->user_id,
            'book_id' => null,
            'barcode_id' => $student->user_id,
            'book_barcode' => 'ACC-9001',
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($student)->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSee('Request History');
        $response->assertSee('Pending');
    }

    public function test_staff_dashboard_shows_archived_request_history_section(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'approved',
            'user_id' => 'STF-9001',
        ]);

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));

        $response->assertOk();
        $response->assertSee('Archived Request History');
    }

    public function test_student_dashboard_does_not_show_barcode_request_section_for_staff_and_admin(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'approved',
            'user_id' => 'STF-9002',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved',
            'user_id' => 'ADM-9001',
        ]);

        $staffView = $this->actingAs($staff)->get(route('student.dashboard'))->getContent();
        $adminView = $this->actingAs($admin)->get(route('student.dashboard'))->getContent();

        $this->assertStringNotContainsString('Request New Barcode', $staffView);
        $this->assertStringNotContainsString('Request New Barcode', $adminView);
    }
}
