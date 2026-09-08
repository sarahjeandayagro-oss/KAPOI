<?php
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "=== Members ===\n";
$members = DB::table('members')->limit(5)->get();
echo "Count: " . count($members) . "\n";
foreach($members as $m) {
    echo "ID: {$m->id}, Barcode: {$m->barcode_id}, Name: {$m->full_name}\n";
}

echo "\n=== Available Barcodes ===\n";
$barcodes = DB::table('available_barcodes')->limit(5)->get();
echo "Count: " . count($barcodes) . "\n";
foreach($barcodes as $b) {
    echo "ID: {$b->id}, Code: {$b->barcode_code}, Status: {$b->status}\n";
}

echo "\n=== Users ===\n";
$users = DB::table('users')->select('id', 'user_id', 'name')->limit(5)->get();
echo "Count: " . count($users) . "\n";
foreach($users as $u) {
    echo "ID: {$u->id}, user_id: {$u->user_id}, Name: {$u->name}\n";
}

echo "\n=== Books ===\n";
$books = DB::table('books')->select('id', 'title', 'isbn', 'accession_number', 'available')->limit(5)->get();
echo "Count: " . count($books) . "\n";
foreach($books as $b) {
    echo "ID: {$b->id}, Title: {$b->title}, ISBN: {$b->isbn}, Accession: {$b->accession_number}, Available: {$b->available}\n";
}

echo "\n=== Borrowing Transactions ===\n";
$borrows = DB::table('borrowing_transactions')->limit(5)->get();
echo "Count: " . count($borrows) . "\n";
foreach($borrows as $b) {
    echo "ID: {$b->id}, user_id: {$b->user_id}, member_id: {$b->member_id}, barcode: {$b->barcode_id}\n";
}
