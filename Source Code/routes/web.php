<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;

/*
|--------------------------------------------------------------------------
| 1. Public Routes (Anyone can access)
|--------------------------------------------------------------------------
*/

// Home Page: Displays medical news, diseases info, and sitemap link
Route::get('/', [PageController::class, 'home'])->name('home');

// About Us & Contact Us
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit');

// Sitemap Page
Route::get('/sitemap', [PageController::class, 'sitemap'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| 2. Authentication Routes
|--------------------------------------------------------------------------
*/
Auth::routes(); //
Route::get('/home', function() { return redirect('/'); });

/*
|--------------------------------------------------------------------------
| 3. Authenticated Routes (Requires Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // --- Common: Profile Management (Đã điều chỉnh tách biệt Password) ---
    Route::prefix('profile')->name('profile.')->group(function () {
        // Trang quản lý thông tin cá nhân
        Route::get('/', [HomeController::class, 'index'])->name('index');
        Route::post('/update', [HomeController::class, 'updateProfile'])->name('update');
        
        // Trang đổi mật khẩu chuyên biệt (Tách ra theo yêu cầu giảng viên)
        Route::get('/password', function() { return view('profile.password'); })->name('password');
        Route::post('/password/update', [HomeController::class, 'updatePassword'])->name('password.update');
        
        // Quản lý ảnh đại diện
        Route::post('/avatar', [HomeController::class, 'uploadAvatar'])->name('avatar');
    });

    // --- Administrator Module ---
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Master Database: City Management 
        Route::post('/cities', [AdminController::class, 'storeCity'])->name('cities.store');
        Route::delete('/cities/{id}', [AdminController::class, 'destroyCity'])->name('cities.destroy');
        
        // Content Management: News, Diseases, Cures
        Route::post('/articles', [AdminController::class, 'storeArticle'])->name('articles.store');
        Route::delete('/articles/{id}', [AdminController::class, 'destroyArticle'])->name('articles.destroy');
        
        // User & Patient Management 
        Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
        
        // Doctor Management
        Route::get('/doctors', [AdminController::class, 'manageDoctors'])->name('doctors.index');
        Route::delete('/doctors/{id}', [AdminController::class, 'destroyDoctor'])->name('doctors.destroy');
    });

    // --- Doctor Module ---
    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
        
        // Availability Scheduling
        Route::get('/schedule', [DoctorController::class, 'schedule'])->name('schedule');
        Route::post('/schedule', [DoctorController::class, 'storeSchedule'])->name('schedule.store');
        Route::put('/schedule/{id}', [DoctorController::class, 'updateSchedule'])->name('schedule.update');
        Route::delete('/schedule/{id}', [DoctorController::class, 'destroySchedule'])->name('schedule.destroy');
        
        // View & Manage Appointments
        Route::get('/appointments', [DoctorController::class, 'appointments'])->name('appointments');
        Route::post('/appointments/{id}/confirm', [DoctorController::class, 'confirmAppointment'])->name('appointment.confirm');
        Route::post('/appointments/{id}/cancel', [DoctorController::class, 'cancelAppointment'])->name('appointment.cancel');
    });

    // --- Patient Module ---
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
        
        // Search & View Doctor Profile
        Route::get('/search', [PatientController::class, 'index'])->name('index');
        
        // Appointment Booking & Management
        Route::post('/book', [PatientController::class, 'book'])->name('book');
        Route::get('/appointments', [PatientController::class, 'appointments'])->name('appointments');
        Route::post('/appointments/{id}/cancel', [PatientController::class, 'cancel'])->name('cancel');
    });
});