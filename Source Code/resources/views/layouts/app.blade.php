<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MediConnect - Healthcare Agency</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-dark: #0f4c81;
            --primary-light: #e6f0fa;
            --bg-gray: #f4f7f6;
        }
        body { background-color: var(--bg-gray); font-family: 'Segoe UI', system-ui, sans-serif; }
        .bg-primary-dark { background-color: var(--primary-dark) !important; color: white; }
        .btn-primary-dark { background-color: var(--primary-dark); color: white; border: none; border-radius: 8px; transition: 0.3s; }
        .btn-primary-dark:hover { background-color: #0b3861; color: white; }
        .navbar-brand { font-weight: 700; color: var(--primary-dark) !important; }
        .nav-link { font-weight: 500; color: #555; }
        .nav-link:hover { color: var(--primary-dark); }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <i class="bi bi-capsule-pill me-2 text-primary"></i> MediConnect
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('sitemap') }}">Sitemap</a></li>
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-center">
                        @if(!Auth::check() || Auth::user()->role == 'patient' || Auth::user()->role == 1)
                            <li class="nav-item me-3">
                                <a href="{{ route('patient.index') }}" class="btn btn-primary-dark px-4 shadow-sm">
                                    <i class="bi bi-calendar-check me-1"></i> Book Appointment
                                </a>
                            </li>
                        @endif

                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <img src="{{ Auth::user()->avatar ? asset('uploads/avatars/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" 
                                         class="rounded-circle me-2" width="32" height="32" alt="Avatar">
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 0 || Auth::user()->role == 3)
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-shield-lock me-2"></i> Admin Dashboard
                                        </a>
                                    @elseif(Auth::user()->role == 'doctor' || Auth::user()->role == 2)
                                        <a class="dropdown-item" href="{{ route('doctor.dashboard') }}">
                                            <i class="bi bi-person-badge me-2"></i> Doctor Dashboard
                                        </a>
                                    @else
                                        <a class="dropdown-item" href="{{ route('patient.index') }}">
                                            <i class="bi bi-search me-2"></i> Find a Doctor
                                        </a>
                                        <a class="dropdown-item" href="{{ route('patient.appointments') }}">
                                            <i class="bi bi-calendar-check me-2"></i> My Appointments
                                        </a>
                                    @endif

                                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                                        <i class="bi bi-gear me-2"></i> Account Settings
                                    </a>
                                    
                                    <hr class="dropdown-divider">
                                    
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        <footer class="bg-white py-4 border-top mt-auto">
            <div class="container text-center">
                <p class="mb-0 text-muted">&copy; 2026 MediConnect Healthcare Group. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>