<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;

// Create a member with barcode for testing
DB::table('members')->insertOrIgnore([
    'user_id' => null,
    'barcode_id' => '100000013',
    'full_name' => 'Test Borrower',
    'email' => 'borrower@test.com',
    'phone' => '09101234567',
    'membership_type' => 'Regular',
    'status' => 'Active',
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✓ Created test member with barcode: 100000013\n";

// Show all members
$members = DB::table('members')->limit(10)->get();
echo "\nAll members:\n";
foreach($members as $m) {
    echo "- {$m->barcode_id}: {$m->full_name}\n";
}

// Show all users (for reference)
$users = DB::table('users')->select('id', 'user_id', 'name')->limit(10)->get();
echo "\nAll users:\n";
foreach($users as $u) {
    echo "- user_id: {$u->user_id}, name: {$u->name}\n";
}

// Show all books
$books = DB::table('books')->select('id', 'title', 'isbn', 'accession_number', 'available')->limit(10)->get();
echo "\nAll books:\n";
foreach($books as $b) {
    echo "- ISBN:{$b->isbn}, Accession:{$b->accession_number}, Title:{$b->title}, Available: {$b->available}\n";
}
