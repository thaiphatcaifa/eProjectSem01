<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Article;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Appointment;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Khởi tạo bảo vệ Controller bằng Middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Hiển thị trang quản trị với các thống kê và dữ liệu quản lý.
     */
    public function dashboard(Request $request)
    {
        // 1. Xử lý lọc thống kê theo thời gian
        $query = Appointment::query();
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // 2. Các con số thống kê
        $totalAppointments = $query->count();
        $completedAppointments = (clone $query)->where('status', 'Completed')->count();
        $cancelledAppointments = (clone $query)->where('status', 'Cancelled')->count();
        $pendingAppointments = (clone $query)->where('status', 'Pending')->count();
        
        $totalPatients = User::whereIn('role', ['patient', 1, '1'])->count();
        $totalDoctors = Doctor::count();

        // 3. Lấy dữ liệu danh sách quản lý
        $cities = City::all();
        $specialties = Specialty::all(); 
        $articles = Article::orderBy('created_at', 'desc')->get();
        $users = User::where('role', '!=', 'admin')->get(); 
        
        // Lấy tất cả user có quyền bác sĩ (role 2) để hiển thị trong tab quản lý bác sĩ
        $doctors = User::whereIn('role', ['doctor', 2, '2'])->with('doctor.specialty')->get();

        return view('admin.dashboard', compact(
            'totalAppointments', 'completedAppointments', 'cancelledAppointments', 'pendingAppointments',
            'totalPatients', 'totalDoctors', 'cities', 'specialties', 'articles', 'users', 'doctors'
        ));
    }

    // --- Quản lý Thành phố (Do Admin tạo) ---
    public function storeCity(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:cities,name']);
        City::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'City added successfully!');
    }

    public function destroyCity($id)
    {
        City::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'City deleted successfully!');
    }

    // --- Quản lý Chuyên khoa (Do Admin tạo) ---
    public function storeSpecialty(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:specialties,name']);
        Specialty::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Specialty added successfully!');
    }

    public function destroySpecialty($id)
    {
        $specialty = Specialty::findOrFail($id);
        
        // Kiểm tra an toàn: Không cho xóa nếu đang có bác sĩ thuộc chuyên khoa này
        $doctorCount = Doctor::where('specialty_id', $id)->count();
        if ($doctorCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete this specialty because it is currently assigned to ' . $doctorCount . ' doctor(s).');
        }

        $specialty->delete();
        return redirect()->back()->with('success', 'Specialty deleted successfully!');
    }

    // --- Quản lý Nội dung ---
    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:news,disease,prevention',
        ]);
        Article::create($request->all());
        return redirect()->back()->with('success', 'Content published successfully!');
    }

    public function destroyArticle($id)
    {
        Article::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Content deleted successfully!');
    }

    // --- Quản lý User & Trạng thái ---
    
    /**
     * NÂNG CẤP: Đổi trạng thái hoạt động của User dựa trên cột is_active
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Đảo ngược trạng thái hoạt động (true -> false, false -> true)
        $user->is_active = !$user->is_active;
        $user->save();
        
        // Tùy chỉnh câu thông báo
        $statusMsg = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', 'User account has been ' . $statusMsg . '!');
    }

    /**
     * Phê duyệt nâng cấp lên Bác sĩ (Role 2)
     */
    public function upgradeToDoctor(Request $request, $id)
    {
        $request->validate([
            'specialty_id' => 'required|exists:specialties,id', 
            'city_id' => 'required|exists:cities,id',           
            'hospital_name' => 'required|string|max:255',
            'consultation_fee' => 'required|numeric|min:0'
        ]);

        $user = User::findOrFail($id);
        
        // 1. Cập nhật User (Role -> 2, và gán City)
        $user->role = 2; // Sử dụng role số cho đồng bộ
        $user->city_id = $request->city_id; 
        $user->is_requesting_doctor = false; 
        $user->save();

        // 2. Tạo hoặc cập nhật hồ sơ bác sĩ (Gán Specialty)
        Doctor::updateOrCreate(
            ['user_id' => $user->id],
            [
                'specialty_id' => $request->specialty_id,
                'hospital_name' => $request->hospital_name,
                'consultation_fee' => $request->consultation_fee
            ]
        );

        return redirect()->back()->with('success', 'User ' . $user->name . ' has been upgraded to Doctor successfully!');
    }

    public function manageDoctors()
    {
        $doctors = User::whereIn('role', ['doctor', 2, '2'])->with('doctor.specialty')->get();
        return view('admin.doctors', compact('doctors'));
    }

    public function destroyDoctor($id)
    {
        // Khi xóa hồ sơ bác sĩ, ta giáng cấp họ về làm bệnh nhân (role 1)
        $doctor = Doctor::findOrFail($id);
        $user = User::find($doctor->user_id);
        if($user) {
            $user->role = 1;
            $user->save();
        }
        $doctor->delete();
        return redirect()->back()->with('success', 'Doctor profile removed and user demoted to patient.');
    }
}