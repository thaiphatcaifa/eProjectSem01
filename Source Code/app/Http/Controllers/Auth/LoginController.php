<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException; // Bổ sung thư viện để hiển thị lỗi

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Xử lý điều hướng dựa trên vai trò sau khi đăng nhập thành công.
     */
    protected function authenticated(Request $request, $user)
    {
        // 1. Kiểm tra nếu tài khoản đang bị vô hiệu hóa (Deactivated)
        if (!$user->is_active) {
            Auth::logout(); // Hủy phiên đăng nhập ngay lập tức
            
            // Trả về giao diện Login kèm thông báo lỗi màu đỏ ở ô Email
            throw ValidationException::withMessages([
                $this->username() => ['Your account has been deactivated. Please contact the administration.'],
            ]);
        }

        // 2. Chuyển hướng người dùng dựa trên vai trò (Nếu tài khoản đang hoạt động)
        // Kiểm tra quyền Admin (hỗ trợ cả giá trị số 3, 0 hoặc chuỗi 'admin')
        if ($user->role == 3 || $user->role == 0 || $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } 
        
        // Kiểm tra quyền Bác sĩ (hỗ trợ giá trị số 2 hoặc chuỗi 'doctor')
        if ($user->role == 2 || $user->role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        } 
        
        // Mặc định điều hướng về khu vực Bệnh nhân (Role 1)
        return redirect()->route('patient.index'); 
    }
}