<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model {
    // Bổ sung 'cancel_reason' vào mảng $fillable
    protected $fillable = ['patient_id', 'doctor_id', 'schedule_id', 'status', 'cancel_reason'];

    public function schedule() { 
        return $this->belongsTo(DoctorSchedule::class); 
    }
    
    public function patient() { 
        return $this->belongsTo(User::class, 'patient_id'); 
    }

    // Add this missing relationship
    public function doctor() {
        return $this->belongsTo(Doctor::class);
    }
}