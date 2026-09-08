<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'user_id' => 'STU-0000',
            'role' => 'student',
            'status' => 'approved',
            'password' => Hash::make('password'),
        ]);

        User::updateOrCreate([
            'email' => 'admin@pcl.test',
        ], [
            'name' => 'Panabo Admin',
            'user_id' => 'ADMIN-001',
            'role' => 'admin',
            'status' => 'approved',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ]);

        User::updateOrCreate([
            'email' => 'staff@pcl.test',
        ], [
            'name' => 'Library Staff',
            'user_id' => 'STAFF-001',
            'role' => 'staff',
            'status' => 'approved',
            'password' => Hash::make('staff123'),
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ]);

        User::updateOrCreate([
            'email' => 'researcher@pcl.test',
        ], [
            'name' => 'Researcher Account',
            'user_id' => 'RES-001',
            'role' => 'researcher',
            'status' => 'approved',
            'password' => Hash::make('researcher123'),
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ]);

        Member::updateOrCreate(
            ['barcode_id' => '202600123'],
            [
                'full_name' => 'Juan Dela Cruz',
                'school' => 'Davao del Norte State College',
                'profile_picture' => null,
                'wifi_voucher' => 'ABC123XYZ',
                'status' => 'active',
            ]
        );

        Member::updateOrCreate(
            ['barcode_id' => '202600124'],
            [
                'full_name' => 'Maria Santos',
                'school' => 'Panabo City National High School',
                'profile_picture' => null,
                'wifi_voucher' => 'PCL-WIFI-824',
                'status' => 'active',
            ]
        );

        Member::updateOrCreate(
            ['barcode_id' => '202600125'],
            [
                'full_name' => 'Reynaldo Garcia',
                'school' => 'University of Mindanao Tagum College',
                'profile_picture' => null,
                'wifi_voucher' => 'LIB-2026-551',
                'status' => 'active',
            ]
        );

        Member::updateOrCreate(
            ['barcode_id' => '202600126'],
            [
                'full_name' => 'Ana Mae Villanueva',
                'school' => 'Davao del Norte State College',
                'profile_picture' => null,
                'wifi_voucher' => 'READ-7731',
                'status' => 'inactive',
            ]
        );

        foreach ([
            '202600200',
            '202600201',
            '202600202',
            '202600203',
            '202600204',
        ] as $barcodeId) {
            Member::updateOrCreate(
                ['barcode_id' => $barcodeId],
                [
                    'full_name' => 'Unassigned',
                    'school' => null,
                    'profile_picture' => null,
                    'wifi_voucher' => null,
                    'status' => 'available',
                ]
            );
        }

        foreach ([
            ['voucher_code' => 'ABC123XYZ', 'status' => 'Available'],
            ['voucher_code' => 'PCL-WIFI-824', 'status' => 'Available'],
            ['voucher_code' => 'LIB-2026-551', 'status' => 'Available'],
            ['voucher_code' => 'PCL-GUEST-001', 'status' => 'Available'],
        ] as $voucher) {
            $voucherPayload = [
                'status' => $voucher['status'],
                'duration_minutes' => 120,
                'bandwidth_gb' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('wifi_vouchers', 'is_active')) {
                $voucherPayload['is_active'] = true;
            }

            DB::table('wifi_vouchers')->updateOrInsert(
                ['voucher_code' => $voucher['voucher_code']],
                $voucherPayload
            );
        }

        foreach ([
            [
                'transaction_type' => 'Fine',
                'patron_name' => 'Juan Dela Cruz',
                'user_id' => '202600123',
                'description' => 'Overdue book fine',
                'amount' => 20,
                'status' => 'Unpaid',
            ],
            [
                'transaction_type' => 'Fund Income',
                'patron_name' => null,
                'user_id' => null,
                'description' => 'Opening library fund balance',
                'amount' => 5000,
                'status' => 'Available',
            ],
        ] as $transaction) {
            DB::table('financial_transactions')->updateOrInsert(
                [
                    'transaction_type' => $transaction['transaction_type'],
                    'description' => $transaction['description'],
                    'user_id' => $transaction['user_id'],
                ],
                [
                    ...$transaction,
                    'transaction_date' => now()->toDateString(),
                    'created_at' => now(),
                'updated_at' => now(),
                ]
            );
        }

        foreach ([
            [
                'barcode' => 'LIB-001',
                'isbn' => '9780000000001',
                'barcode' => 'BC-2026-0001',
                'title' => 'Introduction to Library Science',
                'author' => 'Maria L. Santos',
                'category' => 'Cir (Circulation)',
                'summary' => 'A beginner-friendly overview of cataloging, reference services, and circulation workflows.',
                'call_number' => '020 SAN',
                'accession_number' => 'ACC-2026-0001',
                'publisher' => 'PCL Press',
                'place_of_publication' => 'Panabo City',
                'pages' => 214,
                'section_location' => 'General Stack A-1',
                'copies' => 3,
                'available' => 3,
                'status' => 'Available',
            ],
            [
                'isbn' => '9780000000001',
                'barcode' => 'BC-2026-0002',
                'title' => 'Digital Research Methods for Students',
                'author' => 'Jose M. Rivera',
                'category' => 'F (Fiction)',
                'summary' => 'Practical guidance on organizing sources, writing proposals, and presenting academic work.',
                'call_number' => '001.42 RIV',
                'accession_number' => 'ACC-2026-0002',
                'publisher' => 'PCL Press',
                'place_of_publication' => 'Panabo City',
                'pages' => 178,
                'section_location' => 'General Stack B-2',
                'copies' => 2,
                'available' => 2,
                'status' => 'Available',
            ],
            [
                'isbn' => '9780000000002',
                'barcode' => 'BC-2026-0003',
                'title' => 'A Study on Barcode-Based Attendance Systems',
                'author' => 'Ana P. Dela Cruz',
                'category' => 'T (Thesis)',
                'summary' => 'A sample thesis record used to demonstrate thesis catalog browsing and OPAC filtering.',
                'call_number' => 'THS-2025-01',
                'accession_number' => 'TH-ACC-0001',
                'publisher' => 'University Press',
                'place_of_publication' => 'Davao City',
                'pages' => 92,
                'section_location' => 'Thesis Shelf T-1',
                'copies' => 1,
                'available' => 1,
                'status' => 'Available',
            ],
            [
                'isbn' => '9780000000002',
                'barcode' => 'BC-2026-0004',
                'title' => 'Library Service Quality and Student Satisfaction',
                'author' => 'Ramon B. Garcia',
                'category' => 'T (Thesis)',
                'summary' => 'Another thesis entry to prove thesis items can be searched by title, author, or category.',
                'call_number' => 'THS-2025-02',
                'accession_number' => 'TH-ACC-0002',
                'publisher' => 'University Press',
                'place_of_publication' => 'Davao City',
                'pages' => 104,
                'section_location' => 'Thesis Shelf T-2',
                'copies' => 1,
                'available' => 1,
                'status' => 'Available',
            ],
            [
                'isbn' => '9780000000003',
                'barcode' => 'BC-2026-0005',
                'title' => 'Panabo Library Research Journal Vol. 1',
                'author' => 'Editorial Board',
                'category' => 'Journal',
                'summary' => 'A journal-style record to demonstrate journal searching in the public catalog.',
                'call_number' => 'JRN-2026-01',
                'accession_number' => 'JRN-ACC-0001',
                'publisher' => 'Research Board',
                'place_of_publication' => 'Panabo City',
                'pages' => 60,
                'section_location' => 'Journal Rack J-1',
                'copies' => 4,
                'available' => 4,
                'status' => 'Available',
            ],
            [
                'isbn' => '9780000000003',
                'barcode' => 'BC-2026-0006',
                'title' => 'Community Studies and Local Archives',
                'author' => 'Panabo Research Group',
                'category' => 'Journal',
                'summary' => 'A second journal entry so the journal category is obvious in search results.',
                'call_number' => 'JRN-2026-02',
                'accession_number' => 'JRN-ACC-0002',
                'publisher' => 'Research Board',
                'place_of_publication' => 'Panabo City',
                'pages' => 72,
                'section_location' => 'Journal Rack J-2',
                'copies' => 2,
                'available' => 2,
                'status' => 'Available',
            ],
        ] as $book) {
            DB::table('books')->updateOrInsert(
                ['accession_number' => $book['accession_number']],
                [
                    'barcode' => $book['barcode'],
                    ...$book,
                    'cover_image' => null,
                    'archived_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Sample borrowing transactions for demo
        try {
            $member123 = Member::where('barcode_id', '202600123')->first();
            $member124 = Member::where('barcode_id', '202600124')->first();

            $book1 = DB::table('books')->where('accession_number', 'ACC-2026-0001')->first();
            $book2 = DB::table('books')->where('accession_number', 'TH-ACC-0001')->first();

            if ($member123 && $book1) {
                DB::table('borrowing_transactions')->updateOrInsert(
                    ['member_id' => $member123->id, 'book_id' => $book1->id, 'borrow_date' => now()->toDateString()],
                    [
                        'member_id' => $member123->id,
                        'user_id' => $member123->barcode_id,
                        'book_id' => $book1->id,
                        'barcode_id' => $member123->barcode_id,
                        'book_barcode' => $book1->accession_number,
                        'borrow_date' => now()->toDateString(),
                        'due_date' => now()->addDays(14)->toDateString(),
                        'status' => 'Borrowed',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                DB::table('books')->where('id', $book1->id)->update(['available' => max(0, ($book1->available ?? 1) - 1), 'status' => 'Borrowed', 'updated_at' => now()]);
            }

            if ($member124 && $book2) {
                DB::table('borrowing_transactions')->updateOrInsert(
                    ['member_id' => $member124->id, 'book_id' => $book2->id, 'borrow_date' => now()->subDays(20)->toDateString()],
                    [
                        'member_id' => $member124->id,
                        'user_id' => $member124->barcode_id,
                        'book_id' => $book2->id,
                        'barcode_id' => $member124->barcode_id,
                        'book_barcode' => $book2->accession_number,
                        'borrow_date' => now()->subDays(20)->toDateString(),
                        'due_date' => now()->subDays(6)->toDateString(),
                        'returned_at' => now()->subDays(5)->toDateString(),
                        'status' => 'Returned',
                        'created_at' => now()->subDays(20),
                        'updated_at' => now()->subDays(5),
                    ]
                );
                DB::table('books')->where('id', $book2->id)->update(['available' => ($book2->copies ?? 1), 'status' => 'Available', 'updated_at' => now()]);
            }
        } catch (
            \Exception $e
        ) {
            // Seed may run in environments without all tables; ignore errors
        }

        // Add another sample fine for demo display
        DB::table('financial_transactions')->updateOrInsert(
            [
                'transaction_type' => 'Fine',
                'description' => 'Damaged book replacement',
                'user_id' => '202600124',
            ],
            [
                'transaction_type' => 'Fine',
                'patron_name' => 'Maria Santos',
                'user_id' => '202600124',
                'description' => 'Damaged book replacement',
                'amount' => 150,
                'status' => 'Unpaid',
                'transaction_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
