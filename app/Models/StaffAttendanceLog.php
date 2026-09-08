<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAttendanceLog extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'role',
        'attendance_date',
        'check_in_at',
        'check_out_at',
        'status',
        'remarks',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
