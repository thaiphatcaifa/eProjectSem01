<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Article;
use App\Models\User;
use App\Models\Doctor;

class AdminController extends Controller
{
    public function dashboard()
    {
        $cities = City::all();
        $articles = Article::latest()->get();
        // Fetch all users including patients and doctors for management
        $users = User::all(); 
        $doctors = Doctor::with('user', 'specialty')->get();

        return view('admin.dashboard', compact('cities', 'articles', 'users', 'doctors'));
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