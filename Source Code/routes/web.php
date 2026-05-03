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

// Home Page: Displays medical news, diseases info, and sitemap link [cite: 56, 61, 149]
Route::get('/', [PageController::class, 'home'])->name('home');

// About Us & Contact Us [cite: 101, 104]
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
Auth::routes(); // [cite: 63, 65, 79, 80]
Route::get('/home', function() { return redirect('/'); });

/*
|--------------------------------------------------------------------------
| 3. Authenticated Routes (Requires Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // --- Common: Profile Management [cite: 66, 69, 70, 73] ---
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('index');
        Route::put('/update', [HomeController::class, 'update'])->name('update');
        Route::post('/avatar', [HomeController::class, 'uploadAvatar'])->name('avatar');
    });

    // --- Administrator Module [cite: 86] ---
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Master Database: City Management 
        Route::post('/cities', [AdminController::class, 'storeCity'])->name('cities.store');
        Route::delete('/cities/{id}', [AdminController::class, 'destroyCity'])->name('cities.destroy');
        
        // Content Management: News, Diseases, Cures [cite: 93, 96]
        Route::post('/articles', [AdminController::class, 'storeArticle'])->name('articles.store');
        Route::delete('/articles/{id}', [AdminController::class, 'destroyArticle'])->name('articles.destroy');
        
        // User & Patient Management 
        Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
        
        // Doctor Management [cite: 88, 90]
        Route::get('/doctors', [AdminController::class, 'manageDoctors'])->name('doctors.index');
        Route::delete('/doctors/{id}', [AdminController::class, 'destroyDoctor'])->name('doctors.destroy');
    });

    // --- Doctor Module [cite: 71] ---
    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
        
        // Availability Scheduling [cite: 74, 75]
        Route::get('/schedule', [DoctorController::class, 'schedule'])->name('schedule');
        Route::post('/schedule', [DoctorController::class, 'storeSchedule'])->name('schedule.store');
        
        // View & Manage Appointments [cite: 76, 77]
        Route::get('/appointments', [DoctorController::class, 'appointments'])->name('appointments');
        Route::post('/appointments/{id}/status', [DoctorController::class, 'updateStatus'])->name('appointment.status');
    });

    // --- Patient Module [cite: 78] ---
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
        
        // Search & View Doctor Profile [cite: 81, 82, 99]
        Route::get('/search', [PatientController::class, 'index'])->name('index');
        
        // Appointment Booking & Management [cite: 83, 85]
        Route::post('/book', [PatientController::class, 'book'])->name('book');
        Route::get('/appointments', [PatientController::class, 'appointments'])->name('appointments');
        Route::post('/appointments/{id}/cancel', [PatientController::class, 'cancel'])->name('cancel');
    });
});