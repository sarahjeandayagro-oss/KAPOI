<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProposalArchiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_archive_and_restore_proposal(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $proposalId = DB::table('proposals')->insertGetId([
            'user_id' => 'researcher-1',
            'title' => 'Sample Proposal',
            'budget' => 5000,
            'approved_grant' => 0,
            'requirements' => 'Checklist attachments submitted.',
            'deadline' => now()->addMonth()->toDateString(),
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.proposals.archive', $proposalId))
            ->assertRedirect();

        $this->assertNotNull(DB::table('proposals')->where('id', $proposalId)->value('archived_at'));

        $this->actingAs($admin)
            ->post(route('admin.proposals.restore', $proposalId))
            ->assertRedirect();

        $this->assertNull(DB::table('proposals')->where('id', $proposalId)->value('archived_at'));
    }
}
