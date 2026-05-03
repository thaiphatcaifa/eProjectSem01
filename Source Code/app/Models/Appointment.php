<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model {
    protected $fillable = ['patient_id', 'doctor_id', 'schedule_id', 'status'];

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