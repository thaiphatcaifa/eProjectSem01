<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\City;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PatientController extends Controller {
    public function index(Request $request) {
        $specialties = Specialty::all();
        $cities = City::all();
        
        $query = Doctor::with(['user.city', 'specialty', 'schedules' => function($q) {
            $q->where('is_booked', false)->orderBy('date', 'asc');
        }]);

        if ($request->filled('specialty_id')) {
            $query->where('specialty_id', $request->specialty_id);
        }

        if ($request->filled('city_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }
        
        // Hỗ trợ tìm kiếm theo tên (AJAX)
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $doctors = $query->get();
        
        // Trả về JSON nếu là request AJAX
        if ($request->ajax()) {
            return response()->json([
                'html' => view('patient.partials.doctor_list', compact('doctors'))->render()
            ]);
        }

        return view('patient.index', compact('doctors', 'specialties', 'cities'));
    }

    public function book(Request $request) {
        try {
            DB::transaction(function () use ($request) {
                $schedule = DoctorSchedule::lockForUpdate()->findOrFail($request->schedule_id);
                if ($schedule->is_booked) throw new \Exception("This schedule is already booked!");

                // Logic chặn đặt lịch nếu đã quá hạn (Expired)
                $timeString = trim(explode('-', $schedule->time_slot)[0]);
                $scheduleTime = Carbon::parse($schedule->date . ' ' . $timeString, 'Asia/Ho_Chi_Minh');
                if ($scheduleTime->isPast()) {
                    throw new \Exception("This schedule has expired and cannot be booked.");
                }

                $schedule->is_booked = true; 
                $schedule->save();

                Appointment::create([
                    'patient_id' => Auth::id(),
                    'doctor_id' => $request->doctor_id,
                    'schedule_id' => $schedule->id,
                    'status' => 'Pending' // Trạng thái ban đầu là Pending chờ bác sĩ xác nhận
                ]);
            });
            // Chuyển hướng ngay đến lịch hẹn
            return redirect()->route('patient.appointments')->with('success', 'Appointment booked successfully! Please wait for the doctor\'s confirmation.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function appointments() {
        $appointments = Appointment::where('patient_id', Auth::id())
                                   ->with('doctor.user', 'schedule')
                                   ->orderBy('created_at', 'desc')->get();
        return view('patient.appointments', compact('appointments'));
    }

    public function cancel($id) {
        $appointment = Appointment::where('id', $id)->where('patient_id', Auth::id())->with('schedule')->firstOrFail();
        
        if($appointment->status == 'Cancelled') {
            return back()->with('error', 'Appointment is already cancelled.');
        }
        
        if ($appointment->status == 'Confirmed') {
            // Set rõ timezone Việt Nam để so sánh chính xác tuyệt đối
            $timeString = trim(explode('-', $appointment->schedule->time_slot)[0]);
            $scheduleDate = Carbon::parse($appointment->schedule->date . ' ' . $timeString, 'Asia/Ho_Chi_Minh');
            $now = Carbon::now('Asia/Ho_Chi_Minh');
            
            // diffInHours sẽ trả về số âm nếu $scheduleDate ở trong quá khứ
            if ($now->diffInHours($scheduleDate, false) < 24) {
                 return back()->with('error', 'You can only cancel confirmed appointments at least 24 hours in advance.');
            }
        }
        
        $appointment->status = 'Cancelled';
        $appointment->save();
        
        // Free up the schedule
        $schedule = DoctorSchedule::find($appointment->schedule_id);
        if($schedule) {
            $schedule->is_booked = false;
            $schedule->save();
        }
        return back()->with('success', 'Appointment cancelled successfully.');
    }
}