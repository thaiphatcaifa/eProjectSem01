<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Article;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Appointment;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // 1. XỬ LÝ LỌC THỐNG KÊ THEO THỜI GIAN (Ngày bắt đầu - Ngày kết thúc)
        $query = Appointment::query();
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // 2. CÁC CON SỐ THỐNG KÊ CÓ Ý NGHĨA
        $totalAppointments = $query->count();
        $completedAppointments = (clone $query)->where('status', 'Completed')->count();
        $cancelledAppointments = (clone $query)->where('status', 'Cancelled')->count();
        $pendingAppointments = (clone $query)->where('status', 'Pending')->count();
        
        $totalPatients = User::where('role', 'patient')->count();
        $totalDoctors = Doctor::count();

        // 3. LẤY DỮ LIỆU CHO CÁC TAB QUẢN LÝ (Giữ nguyên logic cũ của nhóm)
        $cities = City::all();
        $articles = Article::latest()->get();
        // Fetch all users including patients and doctors for management
        $users = User::all(); 
        $doctors = Doctor::with('user', 'specialty')->get();

        return view('admin.dashboard', compact(
            'cities', 'articles', 'users', 'doctors',
            'totalAppointments', 'completedAppointments', 'cancelledAppointments', 'pendingAppointments',
            'totalPatients', 'totalDoctors'
        ));
    }

    // --- City Management ---
    public function storeCity(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:cities']);
        City::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'City added successfully!');
    }

    public function destroyCity($id)
    {
        City::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'City deleted successfully!');
    }

    // --- Content Management ---
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

    // --- User & Patient Management ---
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        // Toggle role to 'deactivated' or restore to 'patient'
        $user->role = ($user->role === 'deactivated') ? 'patient' : 'deactivated';
        $user->save();
        return redirect()->back()->with('success', 'User account status updated!');
    }

    public function manageDoctors()
    {
        $doctors = Doctor::with('user', 'specialty')->get();
        return view('admin.doctors', compact('doctors')); // Cần tạo thêm view admin/doctors.blade.php nếu muốn quản lý riêng
    }

    public function destroyDoctor($id)
    {
        Doctor::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Doctor deleted successfully!');
    }
}