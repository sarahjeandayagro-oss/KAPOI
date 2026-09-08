<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\EntrancePortalController;
use App\Http\Controllers\LibraryOperationsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OpacController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\BorrowingOverdueController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
// Landing page
Route::view('/', 'landing')->name('landing');

// Entrance portal (restore routes so components referencing it don't break)
Route::get('/entrance-portal', [EntrancePortalController::class, 'index'])->name('entrance.portal');
Route::get('/entrance-portal/search', [EntrancePortalController::class, 'search'])->name('entrance.portal.search');
Route::post('/entrance-portal/scan', [EntrancePortalController::class, 'scan'])->name('entrance.portal.scan');
Route::post('/entrance-portal/borrow', [EntrancePortalController::class, 'borrowByBarcode'])->name('entrance.portal.borrow');

// OPAC — public access catalog (no auth required)
Route::get('/opac', [OpacController::class, 'index'])->name('opac.index');
Route::get('/opac/search', [OpacController::class, 'search'])->name('opac.search');
Route::get('/opac/books/{id}', [OpacController::class, 'show'])->name('opac.book');

// Barcode generation (public, no auth required)
Route::get('/barcode/book/{barcode}', [BarcodeController::class, 'generateBook'])->name('barcode.book.png');

// API endpoints (public, no auth required)
Route::get('/api/books', [OpacController::class, 'getAllBooks'])->name('api.books');

Route::view('/login', 'login')->name('login');
Route::post('/login-submit', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.submit');
Route::post('/login-ajax', [AuthController::class, 'ajaxLogin'])
    ->middleware('throttle:5,1')
    ->name('login.ajax');
Route::get('/register', function () {
    return view('register', ['schoolOptions' => AuthController::schoolOptions()]);
})->name('register');
Route::get('/email/verify', [AuthController::class, 'showEmailVerificationForm'])->name('email.verify.form');
Route::post('/email/verify', [AuthController::class, 'verifyEmail'])->name('email.verify');
Route::post('/register-submit', [AuthController::class, 'register'])
    ->middleware('throttle:3,1')
    ->name('register.submit');

Route::post('/logout', function () {
    Auth::logout();

    return redirect('/login');
})->name('logout');

Route::get('/__debug/url-scheme', function () {
    abort_unless(config('app.debug') || env('APP_DEBUG_URL_DIAGNOSTICS', false), 404);

    $payload = [
        'is_secure' => request()->isSecure(),
        'scheme' => request()->getScheme(),
        'http_host' => request()->getHttpHost(),
        'host' => request()->getHost(),
        'x_forwarded_proto' => request()->header('x-forwarded-proto'),
        'x_forwarded_host' => request()->header('x-forwarded-host'),
        'app_url' => config('app.url'),
        'url_root' => url('/'),
        'url_current' => url()->current(),
        'asset_css' => asset('css/app.css'),
        'route_login' => route('login'),
        'route_admin' => route('admin.dashboard'),
    ];

    Log::info('url-scheme-debug', $payload);

    return response()->json($payload);
})->name('debug.url-scheme');

Route::middleware(['auth'])->group(function () {
    // Staff attendance recording and visitor barcode creation removed from routes

    Route::get('/admin-dashboard', [LibraryOperationsController::class, 'adminDashboard'])
        ->middleware('role:admin')
        ->name('admin.dashboard');
    Route::get('/admin/profile', [LibraryOperationsController::class, 'profile'])
        ->middleware('role:admin')
        ->name('admin.profile');
    Route::get('/staff-dashboard', [StaffController::class, 'index'])
        ->middleware('role:staff,admin')
        ->name('staff.dashboard');
    Route::get('/student-dashboard', [LibraryOperationsController::class, 'studentDashboard'])
        ->middleware('role:student,visitor,admin')
        ->name('student.dashboard');
    Route::get('/visitor-dashboard', [LibraryOperationsController::class, 'studentDashboard'])
        ->middleware('role:visitor')
        ->name('visitor.dashboard');
    Route::get('/researcher-dashboard', [ProposalController::class, 'researcherIndex'])
        ->middleware('role:researcher,admin')
        ->name('researcher.dashboard');

    Route::get('/student/profile', [LibraryOperationsController::class, 'profile'])->name('student.profile');
    Route::get('/visitor/profile', [LibraryOperationsController::class, 'profile'])->name('visitor.profile');
    Route::get('/researcher/profile', [LibraryOperationsController::class, 'profile'])->name('researcher.profile');
    Route::get('/staff/profile', [LibraryOperationsController::class, 'profile'])->name('staff.profile');
    Route::get('/profile/edit', [LibraryOperationsController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [LibraryOperationsController::class, 'updateProfile'])->name('profile.update');
    Route::get('/books/lookup', [LibraryOperationsController::class, 'bookLookup'])
        ->middleware('role:admin,staff')
        ->name('books.lookup');
    Route::view('/barcode-printer', 'barcode-printer')
        ->middleware('role:admin,staff')
        ->name('barcode.printer');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Student/Visitor book borrowing requests
    Route::post('/opac/request-borrow', [LibraryOperationsController::class, 'requestBorrow'])
        ->middleware('role:student,visitor')
        ->name('opac.request-borrow');

    // Student/Visitor barcode requests
    Route::post('/student/barcode-request', [LibraryOperationsController::class, 'storeBarcodeRequest'])
        ->middleware('role:student')
        ->name('student.barcode-request.store');
    Route::post('/visitor/barcode-request', [LibraryOperationsController::class, 'storeBarcodeRequest'])
        ->middleware('role:visitor')
        ->name('visitor.barcode-request.store');

    Route::post('/submit-proposal', [ProposalController::class, 'store'])
        ->middleware('role:researcher,admin')
        ->name('proposal.submit');
    Route::get('/submit-proposal', function () {
        return redirect()->route('researcher.dashboard')
            ->withErrors(['proposal_error' => 'Your session may have expired. Please try submitting again.']);
    });
    Route::get('/proposal-documents/{documentId}', [ProposalController::class, 'downloadDocument'])
        ->name('proposal.documents.download');
    // Inline viewer for proposal documents (opens in browser when supported)
    Route::get('/proposal-documents/{documentId}/view', [ProposalController::class, 'viewDocumentById'])
        ->name('proposal.documents.view');
    Route::post('/researcher/proposals/{id}/archive', [ProposalController::class, 'archive'])->name('researcher.proposals.archive');
    Route::post('/researcher/proposals/{id}/restore', [ProposalController::class, 'restore'])->name('researcher.proposals.restore');
    Route::post('/research-funds', [ProposalController::class, 'storeFundTransaction'])
        ->middleware('role:admin,staff,researcher')
        ->name('research-funds.store');
    Route::post('/research-funds/{id}/review', [ProposalController::class, 'reviewFundTransaction'])
        ->middleware('role:admin,staff')
        ->name('research-funds.review');

    // Staff & Admin shared routes (daily library operations)
    Route::middleware('role:admin,staff')->group(function () {
        Route::post('/admin/proposals/{id}/status', [ProposalController::class, 'updateStatus'])->name('admin.proposals.status');
        Route::get('/admin/proposals/{id}/pdf', [ProposalController::class, 'showPdf'])->name('admin.proposals.pdf');
        Route::post('/admin/proposals/{id}/comments', [ProposalController::class, 'storeComment'])->name('admin.proposals.comments');
        Route::get('/admin/proposals/{id}/comments', [ProposalController::class, 'getComments'])->name('admin.proposals.comments.list');
        Route::post('/admin/proposals/{id}/archive', [ProposalController::class, 'archive'])->name('admin.proposals.archive');
        Route::post('/admin/proposals/{id}/restore', [ProposalController::class, 'restore'])->name('admin.proposals.restore');
        Route::post('/admin/books/add', [LibraryOperationsController::class, 'storeBook'])->name('admin.books.add');
        Route::post('/admin/books/{id}/update', [LibraryOperationsController::class, 'updateBook'])->name('admin.books.update');
        Route::post('/admin/books/{id}/delete', [LibraryOperationsController::class, 'deleteBook'])->name('admin.books.delete');
        // WiFi voucher management routes (create/update vouchers)
        Route::post('/admin/wifi-vouchers', [LibraryOperationsController::class, 'storeVoucher'])->name('admin.wifi-vouchers.store');
        Route::post('/admin/wifi-vouchers/{id}', [LibraryOperationsController::class, 'updateVoucher'])->name('admin.wifi-vouchers.update');
        Route::post('/admin/financial-transactions', [LibraryOperationsController::class, 'storeFinancialTransaction'])->name('admin.financial-transactions.store');
        Route::post('/admin/financial-transactions/{id}/archive', [LibraryOperationsController::class, 'archiveFinancialTransaction'])->name('admin.financial-transactions.archive');
        Route::post('/admin/borrow-by-barcode', [LibraryOperationsController::class, 'borrowByBarcode'])->name('admin.borrow-by-barcode');
        Route::post('/staff/borrow-by-barcode', [LibraryOperationsController::class, 'borrowByBarcode'])->name('staff.borrow-by-barcode');
        Route::post('/admin/borrowings/{id}/return', [LibraryOperationsController::class, 'returnBorrowing'])->name('admin.borrowings.return');
        Route::post('/admin/borrowings/{id}/overdue', [LibraryOperationsController::class, 'markBorrowingOverdue'])->name('admin.borrowings.overdue');
        // Mark a borrowing as "Due" and waive any related overdue fine (staff action)
        Route::post('/admin/borrowings/{id}/mark-due', [LibraryOperationsController::class, 'markBorrowingDue'])->name('admin.borrowings.mark_due');
        Route::post('/admin/borrow-requests/{id}/approve', [LibraryOperationsController::class, 'approveBorrowRequest'])->name('admin.borrow-requests.approve');
        Route::post('/admin/borrow-requests/{id}/reject', [LibraryOperationsController::class, 'rejectBorrowRequest'])->name('admin.borrow-requests.reject');
        Route::post('/admin/borrow-requests/{id}/archive', [LibraryOperationsController::class, 'archiveBorrowRequest'])->name('admin.borrow-requests.archive');
        Route::post('/admin/borrow-requests/{id}/reject', [LibraryOperationsController::class, 'rejectBorrowRequest'])->name('admin.borrow-requests.reject');
        Route::post('/admin/announcements', [LibraryOperationsController::class, 'storeAnnouncement'])->name('admin.announcements.store');
        Route::post('/admin/verify-researcher', [StaffController::class, 'verifyUser'])->name('admin.verify-researcher');
        Route::post('/admin/reject-researcher', [StaffController::class, 'rejectUser'])->name('admin.reject-researcher');
        Route::post('/admin/assign-barcode', [StaffController::class, 'assignBarcode'])->name('admin.assign-barcode');
        Route::post('/admin/barcode-request/{id}/approve', [StaffController::class, 'approveBarcodeRequest'])->name('admin.barcode-request.approve');
        Route::post('/admin/barcode-request/{id}/reject', [StaffController::class, 'rejectBarcodeRequest'])->name('admin.barcode-request.reject');
        Route::post('/admin/barcode-request/{id}/archive', [StaffController::class, 'archiveBarcodeRequest'])->name('admin.barcode-request.archive');
        Route::post('/staff/barcode-request/{id}/approve', [StaffController::class, 'approveBarcodeRequest'])->name('staff.barcode-request.approve');
        Route::post('/staff/barcode-request/{id}/reject', [StaffController::class, 'rejectBarcodeRequest'])->name('staff.barcode-request.reject');
        Route::post('/staff/barcode-request/{id}/archive', [StaffController::class, 'archiveBarcodeRequest'])->name('staff.barcode-request.archive');
        Route::post('/admin/available-barcodes', [StaffController::class, 'storeAvailableBarcode'])->name('admin.available-barcodes.store');
        Route::post('/admin/available-barcodes/{id}', [StaffController::class, 'updateAvailableBarcode'])->name('admin.available-barcodes.update');
        Route::post('/admin/available-barcodes/{id}/delete', [StaffController::class, 'deleteAvailableBarcode'])->name('admin.available-barcodes.delete');
        Route::post('/admin/generate-barcode', [StaffController::class, 'generateAndAssignBarcode'])->name('admin.generate-barcode');
        Route::get('/admin/reports/export', [LibraryOperationsController::class, 'exportReportsPdf'])->name('admin.reports.export');
    });

// Admin-only routes (system control, user management)
Route::middleware('role:admin')->group(function () {
    // Library certificate generation (library clearance)
    Route::post('/admin/certificates/generate', [LibraryOperationsController::class, 'generateCertificate'])
        ->name('admin.certificates.generate');

    Route::post('/admin/backups', [LibraryOperationsController::class, 'backup'])
        ->name('admin.backups.store');

    Route::post('/admin/schools', [LibraryOperationsController::class, 'storeSchoolOption'])
        ->name('admin.schools.store');

    Route::post('/admin/schools/{id}/delete', [LibraryOperationsController::class, 'deleteSchoolOption'])
        ->name('admin.schools.delete');

    Route::post('/admin/users/status', [LibraryOperationsController::class, 'updateUserStatus'])
        ->name('admin.users.status');

    Route::post('/admin/users/store', [LibraryOperationsController::class, 'storeUserAccount'])
        ->name('admin.users.store');

    Route::post('/admin/users/{id}/reset-password', [LibraryOperationsController::class, 'resetUserAccount'])
        ->name('admin.users.reset-password');

    Route::post('/admin/users/{id}/update', [LibraryOperationsController::class, 'updateUserProfile'])
        ->name('admin.users.update');

    Route::get('/admin/users/{id}/edit', [LibraryOperationsController::class, 'editUserProfile'])
        ->name('admin.users.edit');

    Route::post('/admin/users/{id}/delete', [LibraryOperationsController::class, 'deleteUserProfile'])
        ->name('admin.users.delete');
});

});