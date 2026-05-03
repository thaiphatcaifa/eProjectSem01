<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller {
    
    public function dashboard() {
        $user = Auth::user();
        $doctor = $user->doctor; 

        // TỰ ĐỘNG FIX LỖI: Nếu user có role bác sĩ nhưng chưa có hồ sơ trong bảng doctors
        if(!$doctor) {
            // Lấy tạm 1 chuyên khoa đầu tiên trong DB (hoặc mặc định là 1) để tránh lỗi khóa ngoại
            $specialty = Specialty::first();
            $specialtyId = $specialty ? $specialty->id : 1;

            // Tự động tạo hồ sơ bác sĩ mặc định
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'specialty_id' => $specialtyId,
                'hospital_name' => 'MediConnect Hospital', // Tên bệnh viện mặc định
                'consultation_fee' => 500000               // Phí khám mặc định
            ]);
            
            // Tải lại dữ liệu quan hệ cho user
            $user->load('doctor');
        }

        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)->orderBy('date', 'desc')->get();
        $appointments = Appointment::where('doctor_id', $doctor->id)->orderBy('created_at', 'desc')->get();

        return view('doctor.dashboard', compact('schedules', 'appointments'));
    }

    public function storeSchedule(Request $request) {
        $request->validate([
            'date' => 'required|date', 
            'time_slot' => 'required|string'
        ]);
        
        $doctor = Auth::user()->doctor;
        
        if(!$doctor) {
            return back()->with('error', 'Không tìm thấy hồ sơ bác sĩ!');
        }

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'is_booked' => false
        ]);
        
        return back()->with('success', 'Available schedule added successfully!');
    }
}