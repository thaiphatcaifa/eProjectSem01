@extends('layouts.app')

@section('content')
@php
    // Determine user role for UI logic
    $userRole = 'guest';
    if(Auth::check()) {
        $roleMap = [1 => 'patient', 2 => 'doctor', 3 => 'admin'];
        $userRole = is_numeric(Auth::user()->role) ? ($roleMap[Auth::user()->role] ?? 'patient') : strtolower(Auth::user()->role);
    }
@endphp

<div class="container py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary-dark"><i class="bi bi-diagram-3 me-2"></i>Application Sitemap</h2>
        <p class="text-muted">A complete overview of the MediConnect platform structure.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm sitemap-card">
                <div class="card-header bg-primary-dark text-white text-center py-3">
                    <i class="bi bi-globe fs-2 d-block mb-2"></i>
                    <h5 class="mb-0 fw-bold">Public Area</h5>
                </div>
                <div class="card-body px-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 border-light"><a href="{{ route('home') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-house me-2"></i>Home Page</a></li>
                        <li class="list-group-item px-0 border-light"><a href="{{ route('about') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-info-circle me-2"></i>About Us</a></li>
                        <li class="list-group-item px-0 border-light"><a href="{{ route('contact') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-envelope me-2"></i>Contact Us</a></li>
                        <li class="list-group-item px-0 border-light d-flex align-items-center">
                            <i class="bi bi-box-arrow-in-right text-secondary me-2"></i>
                            <a href="{{ route('login') }}" class="text-decoration-none text-secondary custom-link">Login</a> 
                            <span class="mx-1 text-muted">/</span> 
                            <a href="{{ route('register') }}" class="text-decoration-none text-secondary custom-link">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm sitemap-card border-top border-4 border-success">
                <div class="card-header bg-white text-success text-center py-3">
                    <i class="bi bi-person-heart fs-2 d-block mb-2"></i>
                    <h5 class="mb-0 fw-bold">Patient Portal</h5>
                </div>
                <div class="card-body px-4">
                    <ul class="list-group list-group-flush">
                        @if($userRole === 'patient')
                            <li class="list-group-item px-0 border-light"><a href="{{ route('patient.index') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-search me-2 text-success"></i>Search & Book Doctor</a></li>
                            <li class="list-group-item px-0 border-light"><a href="{{ route('patient.appointments') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-calendar-check me-2 text-success"></i>My Appointments</a></li>
                            <li class="list-group-item px-0 border-light"><a href="{{ route('profile.index') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-person-gear me-2 text-success"></i>Account Settings</a></li>
                        @else
                            <li class="list-group-item px-0 border-light text-muted"><i class="bi bi-lock-fill me-2 text-danger"></i>Requires Patient Login</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm sitemap-card border-top border-4 border-info">
                <div class="card-header bg-white text-info text-center py-3">
                    <i class="bi bi-clipboard2-pulse fs-2 d-block mb-2"></i>
                    <h5 class="mb-0 fw-bold">Doctor Portal</h5>
                </div>
                <div class="card-body px-4">
                    <ul class="list-group list-group-flush">
                        @if($userRole === 'doctor')
                            <li class="list-group-item px-0 border-light"><a href="{{ route('doctor.dashboard') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-speedometer2 me-2 text-info"></i>Doctor Dashboard</a></li>
                            <li class="list-group-item px-0 border-light"><a href="{{ route('doctor.dashboard') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-calendar-plus me-2 text-info"></i>Manage Availability</a></li>
                            <li class="list-group-item px-0 border-light"><a href="{{ route('profile.index') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-person-gear me-2 text-info"></i>Account Settings</a></li>
                        @else
                            <li class="list-group-item px-0 border-light text-muted"><i class="bi bi-lock-fill me-2 text-danger"></i>Requires Doctor Login</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm sitemap-card border-top border-4 border-danger">
                <div class="card-header bg-white text-danger text-center py-3">
                    <i class="bi bi-shield-lock fs-2 d-block mb-2"></i>
                    <h5 class="mb-0 fw-bold">Admin Panel</h5>
                </div>
                <div class="card-body px-4">
                    <ul class="list-group list-group-flush">
                        @if($userRole === 'admin')
                            <li class="list-group-item px-0 border-light"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-speedometer me-2 text-danger"></i>Admin Dashboard</a></li>
                            <li class="list-group-item px-0 border-light"><a href="{{ route('admin.dashboard') }}#cities" class="text-decoration-none text-secondary custom-link"><i class="bi bi-geo-alt me-2 text-danger"></i>City Management</a></li>
                            <li class="list-group-item px-0 border-light"><a href="{{ route('admin.dashboard') }}#users" class="text-decoration-none text-secondary custom-link"><i class="bi bi-people me-2 text-danger"></i>User Management</a></li>
                            <li class="list-group-item px-0 border-light"><a href="{{ route('admin.dashboard') }}#content" class="text-decoration-none text-secondary custom-link"><i class="bi bi-file-earmark-text me-2 text-danger"></i>Content Management</a></li>
                            <li class="list-group-item px-0 border-light"><a href="{{ route('profile.index') }}" class="text-decoration-none text-secondary custom-link"><i class="bi bi-person-gear me-2 text-danger"></i>Account Settings</a></li>
                        @else
                            <li class="list-group-item px-0 border-light text-muted"><i class="bi bi-lock-fill me-2 text-danger"></i>Requires Admin Login</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .sitemap-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .sitemap-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1)!important; }
    .custom-link { transition: color 0.2s ease, padding-left 0.2s ease; }
    .custom-link:hover { color: var(--primary-dark) !important; font-weight: 600; padding-left: 4px; }
    .border-light { border-color: #f1f3f5 !important; }
</style>
@endsection