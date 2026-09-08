<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'barcode_id',
        'assigned_user_id',
        'full_name',
        'school',
        'profile_picture',
        'wifi_voucher',
        'status',
    ];

    public function entryLogs()
    {
        return $this->hasMany(EntryLog::class);
    }

    public static function generateUniqueBarcodeId(): string
    {
        // Get the total count of members + users with barcodes + 1000000 base offset
        // This ensures sequential, guaranteed-unique 9-digit barcodes
        $totalBarcodes = self::count() + User::whereNotNull('barcode_id')->count();
        $barcodeNumber = 100000000 + $totalBarcodes; // Start at 100000000, increment by count

        // Ensure we don't exceed 9 digits
        $barcodeNumber = $barcodeNumber % 1000000000; // Keep it under 1 billion

        // Format as 9-digit string
        $barcodeId = str_pad((string) $barcodeNumber, 9, '0', STR_PAD_LEFT);

        // Double-check uniqueness (should never fail with this approach)
        while (self::where('barcode_id', $barcodeId)->exists() || User::where('barcode_id', $barcodeId)->exists()) {
            $barcodeNumber++;
            $barcodeNumber = $barcodeNumber % 1000000000;
            $barcodeId = str_pad((string) $barcodeNumber, 9, '0', STR_PAD_LEFT);
        }

        return $barcodeId;
    }
}
