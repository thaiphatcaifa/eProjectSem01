<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model {
    // Đã bổ sung 'price' vào danh sách fillable
    protected $fillable = [
        'doctor_id', 
        'date', 
        'time_slot', 
        'price', 
        'is_booked'
    ];

    public function doctor() { 
        return $this->belongsTo(Doctor::class); 
    }
}