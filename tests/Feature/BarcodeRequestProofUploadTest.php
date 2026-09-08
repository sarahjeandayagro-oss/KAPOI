<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BarcodeRequestProofUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_barcode_request_with_proof_upload(): void
    {
        Storage::fake('public');

        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'approved',
            'school' => 'Sample School',
        ]);

        $file = UploadedFile::fake()->create('proof.txt', 100, 'txt');

        $response = $this->actingAs($student)
            ->post(route('student.barcode-request.store'), [
                'old_barcode_id' => 'OLD-001',
                'reason' => 'lost',
                'reason_details' => 'My barcode was misplaced.',
                'proof' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('barcode_requests', [
            'user_id' => $student->id,
            'status' => 'pending',
        ]);

        $request = \App\Models\BarcodeRequest::query()->where('user_id', $student->id)->latest()->first();
        $this->assertNotNull($request->proof_path);
        $this->assertTrue(Storage::disk('public')->exists($request->proof_path));
    }
}
