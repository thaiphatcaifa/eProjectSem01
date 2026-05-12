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
    
    // Hàm khởi tạo bảo vệ Controller bằng Middleware
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

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
        
        if($request->ajax()) {
            return view('patient.partials.doctor_list', compact('doctors'))->render();
        }

        return view('patient.index', compact('doctors', 'specialties', 'cities'));
    }

    public function book(Request $request) {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'schedule_id' => 'required|exists:doctor_schedules,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Sử dụng lockForUpdate() để khóa dòng dữ liệu, 
                // ngăn chặn hoàn toàn việc 2 người cùng click trong cùng 1 mili-giây
                $schedule = DoctorSchedule::where('id', $request->schedule_id)
                                        ->where('doctor_id', $request->doctor_id)
                                        ->lockForUpdate()
                                        ->firstOrFail(); // Chỉ fail 404 nếu ID bị hack/sửa bậy

                // Nếu lịch đã bị người khác chọn trước đó, ném ra lỗi thân thiện
                if ($schedule->is_booked) {
                    throw new \Exception("This schedule is being confirmed");
                }

                // Tiến hành đặt lịch
                Appointment::create([
                    'patient_id' => Auth::id(),
                    'doctor_id' => $request->doctor_id,
                    'schedule_id' => $schedule->id,
                    'status' => 'Pending'
                ]);

                // Cập nhật trạng thái lịch thành đã đặt
                $schedule->update(['is_booked' => true]);
            });

            return redirect()->route('patient.appointments')->with('success', 'Appointment booked successfully. Please wait for doctor confirmation.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Đề phòng trường hợp lịch không tồn tại trong Database
            return back()->with('error', 'Schedule not found or invalid.');
        } catch (\Exception $e) {
            // Bắt lỗi Exception do chúng ta ném ra ở trên và trả về cho View
            return back()->with('error', $e->getMessage());
        }
    }

    public function appointments() {
        $appointments = Appointment::where('patient_id', Auth::id())
                                ->with(['doctor.user', 'schedule'])
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

    // Hàm xử lý yêu cầu nâng cấp lên bác sĩ
    public function requestDoctor() {
        $user = Auth::user();
        
        // Chỉ xử lý nếu user thực sự đang là bệnh nhân
        if ($user->role == 'patient' || $user->role == 1) {
            $user->is_requesting_doctor = true;
            $user->save();
            return back()->with('success', 'Your request to become a doctor has been submitted to the Admin for approval.');
        }
        
        return back()->with('error', 'Invalid request.');
    }
}