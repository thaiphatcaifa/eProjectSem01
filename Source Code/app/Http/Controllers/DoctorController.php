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
        // Bổ sung validate cho giá tiền (price)
        $request->validate([
            'date' => 'required|date', 
            'time_slot' => 'required|string',
            'price' => 'required|numeric|min:0'
        ]);
        
        $doctor = Auth::user()->doctor;
        
        if(!$doctor) {
            return back()->with('error', 'Không tìm thấy hồ sơ bác sĩ!');
        }

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'price' => $request->price, // Lưu giá tiền
            'is_booked' => false
        ]);
        
        return back()->with('success', 'Schedule posted successfully!');
    }

    // Bổ sung hàm cập nhật lịch khám (Chỉ được sửa khi chưa có bệnh nhân book)
    public function updateSchedule(Request $request, $id) {
        $request->validate([
            'date' => 'required|date',
            'time_slot' => 'required|string',
            'price' => 'required|numeric|min:0'
        ]);

        $doctor = Auth::user()->doctor;
        $schedule = DoctorSchedule::where('id', $id)->where('doctor_id', $doctor->id)->firstOrFail();

        if ($schedule->is_booked) {
            return back()->with('error', 'Cannot update a schedule that is already booked by a patient.');
        }

        $schedule->update([
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'price' => $request->price
        ]);

        return back()->with('success', 'Schedule updated successfully!');
    }

    // Bổ sung hàm xóa lịch khám (Chỉ được xóa khi chưa có bệnh nhân book)
    public function destroySchedule($id) {
        $doctor = Auth::user()->doctor;
        $schedule = DoctorSchedule::where('id', $id)->where('doctor_id', $doctor->id)->firstOrFail();

        if ($schedule->is_booked) {
            return back()->with('error', 'Cannot delete a schedule that is already booked by a patient.');
        }

        $schedule->delete();
        return back()->with('success', 'Schedule deleted successfully!');
    }

    // Bổ sung hàm bác sĩ xác nhận lịch hẹn (Pending -> Confirmed)
    public function confirmAppointment($id) {
        $doctor = Auth::user()->doctor;
        $appointment = Appointment::where('id', $id)->where('doctor_id', $doctor->id)->firstOrFail();

        if ($appointment->status != 'Pending') {
            return back()->with('error', 'Only pending appointments can be confirmed.');
        }

        $appointment->status = 'Confirmed';
        $appointment->save();

        return back()->with('success', 'Appointment confirmed successfully!');
    }

    // Bổ sung hàm bác sĩ từ chối/hủy lịch hẹn
    public function cancelAppointment($id) {
        $doctor = Auth::user()->doctor;
        $appointment = Appointment::where('id', $id)->where('doctor_id', $doctor->id)->firstOrFail();

        if ($appointment->status == 'Cancelled' || $appointment->status == 'Completed') {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        $appointment->status = 'Cancelled';
        $appointment->save();

        // Giải phóng lịch (Free up the schedule)
        $schedule = DoctorSchedule::find($appointment->schedule_id);
        if($schedule) {
            $schedule->is_booked = false;
            $schedule->save();
        }

        return back()->with('success', 'Appointment cancelled and schedule has been freed up.');
    }
}