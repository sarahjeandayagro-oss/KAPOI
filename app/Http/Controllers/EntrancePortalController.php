<?php

namespace App\Http\Controllers;

use App\Models\EntryLog;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EntrancePortalController extends Controller
{
    public function index()
    {
        return view('entrance-portal', [
            'recentVisitors' => $this->recentVisitors(),
        ]);
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('query', ''));
        if ($query === '') {
            return response()->json([]);
        }

        $members = Member::query()
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('full_name', 'like', "%{$query}%")
                    ->orWhere('barcode_id', 'like', "%{$query}%")
                    ->orWhere('school', 'like', "%{$query}%");
            })
            ->orderByRaw("CASE WHEN full_name LIKE ? THEN 0 ELSE 1 END", ["{$query}%"])
            ->limit(10)
            ->get(['id', 'barcode_id', 'full_name', 'school', 'profile_picture', 'status']);

        $users = User::query()
            ->where('status', 'active')
            ->whereNotNull('email_verified_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('user_id', 'like', "%{$query}%")
                    ->orWhere('barcode_id', 'like', "%{$query}%")
                    ->orWhere('school', 'like', "%{$query}%");
            })
            ->whereIn('role', ['student', 'researcher', 'visitor'])
            ->limit(10)
            ->get(['id', 'user_id', 'name', 'barcode_id', 'school', 'profile_picture', 'status', 'email_verified_at']);

        $results = $members->map(function (Member $member) {
            return [
                'type' => 'member',
                'full_name' => $member->full_name,
                'school' => $member->school,
                'barcode_id' => $member->barcode_id,
                'profile_picture' => $member->profile_picture ? '/storage/' . $member->profile_picture : null,
                'status' => $member->status,
            ];
        })->values();

        foreach ($users as $user) {
            $results->push([
                'type' => 'user',
                'full_name' => $user->name,
                'school' => $user->school,
                'barcode_id' => $user->barcode_id ?? $user->user_id,
                'profile_picture' => $user->profile_picture ? '/storage/' . $user->profile_picture : null,
                'status' => $user->status,
            ]);
        }

        return response()->json($results->take(10));
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'barcode_id' => ['required', 'string', 'max:100'],
        ]);

        $barcodeId = trim($validated['barcode_id']);
        $member = Member::where('barcode_id', $barcodeId)->first();
        $user = null;

        if (! $member) {
            $user = User::where('user_id', $barcodeId)
                ->orWhere('barcode_id', $barcodeId)
                ->orWhere('email', $barcodeId)
                ->orWhere('name', 'like', "%{$barcodeId}%")
                ->first();
        }

        if (! $member && ! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode ID not found.',
            ], 404);
        }

        if (! $member && $user) {
            $memberBarcode = $user->barcode_id ?? $user->user_id ?? $user->email;
            $member = Member::firstOrCreate(
                ['barcode_id' => $memberBarcode],
                [
                    'full_name' => $user->name,
                    'school' => $user->school ?? null,
                    'profile_picture' => $user->profile_picture ?? null,
                    'wifi_voucher' => null,
                    'status' => strtolower($user->status ?? '') === 'pending' ? 'inactive' : 'active',
                ]
            );
        }

        if ($member->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This member account is not active.',
            ], 403);
        }

        $now = now();
        $recentDuplicate = EntryLog::where('member_id', $member->id)
            ->where(function ($query) use ($now) {
                $query->where('entry_time', '>=', $now->copy()->subMinutes(1))
                    ->orWhere('exit_time', '>=', $now->copy()->subMinutes(1));
            })
            ->latest('updated_at')
            ->first();

        if ($recentDuplicate) {
            $log = $recentDuplicate;
            $message = 'Scan already processed within the last minute.';
            $scanType = $log->status === 'Checked Out' ? 'OUT' : 'IN';
        } else {
            $activeLog = EntryLog::where('member_id', $member->id)
                ->whereNull('exit_time')
                ->latest('entry_time')
                ->first();

            if ($activeLog) {
                $activeLog->update([
                    'exit_time' => $now,
                    'status' => 'Checked Out',
                    'remarks' => 'Thank you for visiting Panabo City Library',
                ]);
                $log = $activeLog;
                $message = 'Library exit logged successfully.';
                $scanType = 'OUT';
            } else {
                $log = EntryLog::create([
                'member_id' => $member->id,
                'entry_time' => $now,
                'date' => $now->toDateString(),
                    'status' => 'Checked In',
                    'remarks' => 'Welcome to Panabo City Library',
                ]);
                $message = 'Welcome to Panabo City Library';
                $scanType = 'IN';
            }
        }

        $wifiVoucher = $scanType === 'IN' ? $this->consumeWifiVoucher($member) : $member->wifi_voucher;

        // Erase wifi voucher from the member record and do not return it to the client
        if ($wifiVoucher) {
            $member->wifi_voucher = null;
            $member->save();
            $wifiVoucher = null;
        }

        return response()->json([
            'success' => true,
            'duplicate' => (bool) $recentDuplicate,
            'scan_type' => $scanType,
            'message' => $message,
            'member' => [
                'full_name' => $member->full_name,
                'school' => $member->school,
                'profile_picture' => $member->profile_picture ? '/storage/' . $member->profile_picture : null,
                'library_id_number' => $member->barcode_id,
                'wifi_voucher' => null,
                'entry_date' => $log->entry_time->format('F d, Y'),
                'entry_time' => $log->entry_time->format('h:i A'),
                'exit_time' => $log->exit_time ? $log->exit_time->format('h:i A') : null,
            ],
            'recent_visitors' => $this->recentVisitors(),
        ]);
    }

    private function recentVisitors()
    {
        return EntryLog::with('member')
            ->latest('entry_time')
            ->limit(10)
            ->get()
            ->map(function (EntryLog $log) {
                return [
                    'name' => $log->member->full_name ?? 'Unknown Member',
                    'school' => $log->member->school ?? 'No school listed',
                    'barcode_id' => $log->member->barcode_id ?? '',
                    'entry_time' => $log->entry_time->format('h:i A'),
                    'entry_timestamp' => $log->entry_time->toIso8601String(),
                    'exit_time' => $log->exit_time ? $log->exit_time->format('h:i A') : null,
                    'entry_date' => $log->entry_time->format('M d, Y'),
                    'remarks' => $log->remarks,
                ];
            })
            ->values();
    }

    public function borrowByBarcode(Request $request)
    {
        $validated = $request->validate([
            'barcode_id' => ['required', 'string', 'max:100'],
            'book_reference' => ['required', 'string', 'max:100'],
        ]);

        $result = app(LibraryOperationsController::class)->processBarcodeBorrow(
            trim($validated['barcode_id']),
            trim($validated['book_reference'])
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    private function consumeWifiVoucher(Member $member): ?string
    {
        if (! Schema::hasTable('wifi_vouchers')) {
            return $member->wifi_voucher;
        }

        DB::table('wifi_vouchers')
            ->where('status', 'Available')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update([
                'status' => 'Expired',
                'updated_at' => now(),
            ]);

        $voucher = null;

        if ($member->wifi_voucher) {
            $voucher = DB::table('wifi_vouchers')
                ->where('voucher_code', $member->wifi_voucher)
                ->first();
        }

        if (! $voucher) {
            $voucherQuery = DB::table('wifi_vouchers')
                ->where('status', 'Available')
                ->whereNull('used_at')
                ->where(function ($query) use ($member) {
                    $query->whereNull('user_id')
                        ->orWhere('user_id', $member->barcode_id);
                });

            if (Schema::hasColumn('wifi_vouchers', 'is_active')) {
                $voucherQuery->where('is_active', true);
            }

            $voucher = $voucherQuery
                ->orderByRaw('user_id is null')
                ->first();
        }

        if (! $voucher || $voucher->status !== 'Available') {
            return $member->wifi_voucher;
        }

        DB::table('wifi_vouchers')->where('id', $voucher->id)->update([
            'name' => $member->full_name,
            'user_id' => $member->barcode_id,
            'role' => 'member',
            'status' => 'Used',
            'used_at' => now(),
            'updated_at' => now(),
        ]);

        $member->wifi_voucher = $voucher->voucher_code;
        $member->save();

        return $voucher->voucher_code;
    }
}
