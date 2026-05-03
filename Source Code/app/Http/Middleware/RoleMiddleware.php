<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;
        
        // Map integer roles to string (1: patient, 2: doctor, 3: admin, 0: admin)
        $roleMap = [
            1 => 'patient',
            2 => 'doctor',
            3 => 'admin',
            0 => 'admin', // Dự phòng trường hợp role 0 là admin
        ];

        // Lấy tên quyền hiện tại của user, xử lý in thường và xóa khoảng trắng dư
        $currentRoleStr = is_numeric($userRole) 
            ? ($roleMap[$userRole] ?? 'patient') 
            : strtolower(trim($userRole));

        // Xử lý quyền được yêu cầu (từ routes)
        $expectedRole = strtolower(trim($role));

        // Kiểm tra quyền
        if ($currentRoleStr !== $expectedRole) {
            // Nếu không có quyền, hiển thị lỗi của Middleware
            abort(403, 'Unauthorized action. You do not have permission to access this area. (Middleware Blocked)');
        }

        return $next($request);
    }
}