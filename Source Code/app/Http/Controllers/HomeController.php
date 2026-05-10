<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Hiển thị trang quản lý hồ sơ
     */
    public function index()
    {
        return view('profile.index');
    }

    /**
     * Cập nhật thông tin cá nhân (Tên, Email, SĐT, Địa chỉ)
     * Đã bổ sung Validation chặt chẽ theo yêu cầu giảng viên
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, 
            'phone' => 'nullable|string|max:15|regex:/^([0-9\s\-\+\(\)]*)$/', // Giới hạn ký tự và định dạng số
            'address' => 'nullable|string|max:255',
        ], [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'phone.max' => 'The phone number may not be greater than 15 characters.',
            'phone.regex' => 'The phone number format is invalid.'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return back()->with('success', 'Profile information updated successfully.');
    }

    /**
     * Cập nhật mật khẩu (Tách biệt hoàn toàn với Profile)
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Please enter your current password.',
            'current_password.current_password' => 'The provided password does not match your current records.',
            'password.required' => 'A new password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.'
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    /**
     * Tải lên ảnh đại diện
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'avatar.required' => 'Please select an image to upload.',
            'avatar.image' => 'The file must be an image.',
            'avatar.mimes' => 'Allowed formats: jpeg, png, jpg, gif.',
            'avatar.max' => 'The image size should not exceed 2MB.'
        ]);

        $user = Auth::user();
        if ($request->hasFile('avatar')) {
            // Đặt tên file theo timestamp để tránh trùng
            $avatarName = time().'.'.$request->avatar->extension();  
            $request->avatar->move(public_path('uploads/avatars'), $avatarName);
            
            $user->avatar = $avatarName;
            $user->save();
        }

        return back()->with('success', 'Profile picture updated successfully.');
    }
}