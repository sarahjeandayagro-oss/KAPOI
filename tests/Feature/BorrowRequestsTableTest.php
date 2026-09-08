<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class BorrowRequestsTableTest extends TestCase
{
    public function test_pending_request_table_posts_the_transaction_id_to_the_approval_form(): void
    {
        $view = view('components.borrow-requests-table', [
            'requests' => collect([
                (object) [
                    'id' => 42,
                    'user_name' => 'Jane Doe',
                    'user_id' => 'STU-001',
                    'user_email' => 'jane@example.com',
                    'user_role' => 'student',
                    'title' => 'Clean Code',
                    'author' => 'Robert C. Martin',
                    'accession_number' => 'ACC-100',
                    'created_at' => now(),
                ],
            ]),
            'showActions' => true,
            'statusColumn' => false,
            'emptyMessage' => 'No pending borrow requests yet.',
        ])->render();

        $this->assertStringContainsString('name="borrowing_transaction_id"', $view);
        $this->assertStringContainsString('value="42"', $view);
    }

    public function test_admin_recent_borrowings_render_borrower_name_book_title_and_cover(): void
    {
        $view = view('admin-dashboard', [
            'pendingUsers' => collect(),
            'verifiedUsers' => collect(),
            'availableBarcodes' => collect(),
            'books' => collect(),
            'monthlyBorrowing' => [],
            'recentBorrowings' => collect([
                (object) [
                    'id' => 1,
                    'title' => 'Clean Code',
                    'member_name' => 'Jane Doe',
                    'cover_image' => 'covers/clean-code.jpg',
                    'created_at' => now(),
                ],
            ]),
            'recentActivity' => collect(),
            'topVisitors' => collect(),
            'topWifiUsers' => collect(),
            'gateEntries' => collect(),
            'proposalStats' => collect(),
            'mostActiveStudent' => null,
            'mostActiveResearcher' => null,
            'bookTransactionFeed' => collect(),
            'rejectedBorrowRequests' => collect(),
            'archivedBorrowRequests' => collect(),
            'schoolOptions' => collect(),
            'totalBooks' => 0,
            'activeBorrowers' => 0,
            'returnedToday' => 0,
            'overdueBooks' => 0,
            'femaleCount' => 0,
            'maleCount' => 0,
            'femalePct' => 0,
            'malePct' => 0,
            'totalVisitorsThisMonth' => 0,
            'collectedFines' => 0,
            'unpaidFines' => 0,
            'fundIncome' => 0,
            'fundExpense' => 0,
            'availableFunds' => 0,
            'approvedResearchFunds' => 0,
            'pendingBarcodeRequests' => collect(),
            'approvedBarcodeRequests' => collect(),
            'pendingBorrowRequests' => collect(),
            'approvedBorrowRequests' => collect(),
        ])->withErrors([])->render();

        $this->assertStringContainsString('Jane Doe', $view);
        $this->assertStringContainsString('Clean Code', $view);
        $this->assertStringContainsString('/storage/covers/clean-code.jpg', $view);
    }

    public function test_staff_recent_borrowings_render_borrower_name_book_title_cover_and_due_date(): void
    {
        $view = view('staff-dashboard', [
            'pendingUsers' => collect(),
            'verifiedUsers' => collect(),
            'availableBarcodes' => collect(),
            'books' => collect(),
            'monthlyBorrowing' => [],
            'recentBorrowings' => collect([
                (object) [
                    'id' => 1,
                    'title' => 'Clean Code',
                    'borrower_name' => 'Jane Doe',
                    'cover_image' => 'covers/clean-code.jpg',
                    'due_date' => now()->addDays(7)->toDateString(),
                    'accession_number' => 'ACC-100',
                    'created_at' => now(),
                ],
            ]),
            'recentActivity' => collect(),
            'allUsers' => collect(),
            'pendingBorrowRequests' => collect(),
            'approvedBorrowRequests' => collect(),
            'rejectedBorrowRequests' => collect(),
            'archivedBorrowRequests' => collect(),
            'pendingBarcodeRequests' => collect(),
            'approvedBarcodeRequests' => collect(),
            'rejectedBarcodeRequests' => collect(),
            'borrowings' => collect(),
            'bookTransactionFeed' => collect(),
            'totalBooks' => 0,
            'activeBorrowers' => 0,
            'returnedToday' => 0,
            'overdueBooks' => 0,
            'femaleCount' => 0,
            'maleCount' => 0,
            'femalePct' => 0,
            'malePct' => 0,
            'totalVisitorsThisMonth' => 0,
            'financialRows' => collect(),
            'borrower_full_name' => null,
            'borrower_school' => null,
            'book_title' => null,
            'borrower_id' => null,
            'transaction_time' => null,
            'success' => null,
            'errors' => collect(),
        ])->withErrors([])->render();

        $this->assertStringContainsString('Jane Doe', $view);
        $this->assertStringContainsString('Clean Code', $view);
        $this->assertStringContainsString('/storage/covers/clean-code.jpg', $view);
        $this->assertStringContainsString('Due', $view);
    }
}
