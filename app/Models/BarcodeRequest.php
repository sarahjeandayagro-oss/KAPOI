<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'old_barcode_id',
        'new_barcode_id',
        'status',
        'reason',
        'reason_details',
        'proof_path',
        'staff_notes',
        'approved_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: The user who requested the barcode
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relationship: The staff member who approved the request
     */
    public function approvedByStaff()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
