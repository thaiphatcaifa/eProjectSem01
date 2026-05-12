@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-person-badge text-primary-dark fs-2 me-3"></i>
        <h3 class="text-primary-dark fw-bold mb-0">Profile Management</h3>
    </div>
    
    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div> 
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm hover-pop transition h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold small text-uppercase text-muted">Profile Picture</h5>
                </div>
                <div class="card-body text-center p-4 d-flex flex-column justify-content-center align-items-center">
                    <div>
                        <img src="{{ Auth::user()->avatar ? asset('uploads/avatars/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" 
                             class="rounded-circle mb-4 shadow-sm border" width="150" height="150" style="object-fit: cover;" alt="Avatar">
                    </div>
                    <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="w-100 mt-auto">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="avatar" class="form-control form-control-sm @error('avatar') is-invalid @enderror" required>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary-dark btn-sm w-100">Update Avatar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm hover-pop transition h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold small text-uppercase text-muted">Personal Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', Auth::user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Email Address</label>
                                <input type="email" class="form-control bg-light" value="{{ Auth::user()->email }}" readonly>
                                <div class="form-text">Email cannot be changed for security.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Phone Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', Auth::user()->phone) }}" placeholder="090-xxxx-xxx">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Home Address</label>
                                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" 
                                       value="{{ old('address', Auth::user()->address) }}" placeholder="123 Street, City">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary-dark px-4 shadow-sm">
                                <i class="bi bi-check2-circle me-2"></i>Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 d-none d-md-block"></div> 
        
        <div class="col-md-8">
            {{-- Yêu cầu xác thực Bác sĩ --}}
            @if(Auth::user()->role == 'patient' || Auth::user()->role == 1)
                <div class="card border-0 shadow-sm mb-4 hover-pop transition">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold small text-uppercase text-muted">Doctor Verification</h5>
                    </div>
                    <div class="card-body p-4">
                        @if(!Auth::user()->is_requesting_doctor)
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-bold mb-1">Are you a healthcare professional?</h6>
                                    <p class="text-muted small mb-0">Submit a request to verify your account as a Doctor to manage schedules and patients.</p>
                                </div>
                                <form action="{{ route('profile.requestDoctor') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning shadow-sm fw-bold">
                                        <i class="bi bi-patch-check me-2"></i>Request Verification
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-info border-0 shadow-sm mb-0 d-flex align-items-center">
                                <i class="bi bi-hourglass-split fs-4 me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Request Pending Approval</h6>
                                    <small>Your application to become a doctor is currently being reviewed by the administration.</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            <div class="alert alert-light border shadow-sm mb-4 d-flex justify-content-between align-items-center hover-pop transition">
                <div>
                    <i class="bi bi-shield-lock-fill text-primary-dark me-2"></i>
                    Looking to secure your account? Update your password in the Security Settings.
                </div>
                <a href="{{ route('profile.password') }}" class="btn btn-outline-primary btn-sm px-3 fw-bold">
                    Change Password
                </a>
            </div>
        </div>
    </div>
</div>
@endsection