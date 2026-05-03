<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DoctorSchedule extends Model {
    protected $fillable = ['doctor_id', 'date', 'time_slot', 'is_booked'];
    public function doctor() { return $this->belongsTo(Doctor::class); }
}