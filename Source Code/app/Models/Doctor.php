<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Doctor extends Model {
    protected $fillable = ['user_id', 'specialty_id', 'hospital_name', 'consultation_fee'];
    public function user() { return $this->belongsTo(User::class); }
    public function specialty() { return $this->belongsTo(Specialty::class); }
    public function schedules() { return $this->hasMany(DoctorSchedule::class); }
}