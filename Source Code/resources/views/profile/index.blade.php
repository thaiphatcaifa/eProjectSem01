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

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold small text-uppercase text-muted">Profile Picture</h5>
                </div>
                <div class="card-body text-center p-4">
                    <img src="{{ Auth::user()->avatar ? asset('uploads/avatars/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" 
                         class="rounded-circle mb-3 shadow-sm border" width="150" height="150" style="object-fit: cover;" alt="Avatar">
                    
                    <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="avatar" class="form-control form-control-sm @error('avatar') is-invalid @enderror" accept="image/*" required>
                            @error('avatar')
                                <div class="invalid-feedback text-start">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary-dark btn-sm w-100">
                            <i class="bi bi-upload me-2"></i>Upload New Photo
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary-dark"><i class="bi bi-info-circle me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', Auth::user()->name) }}" placeholder="e.g. John Doe" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', Auth::user()->email) }}" placeholder="example@mail.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Phone Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', Auth::user()->phone) }}" placeholder="e.g. 0912345678 (Max 15 digits)">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Home Address</label>
                                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" 
                                       value="{{ old('address', Auth::user()->address) }}" placeholder="123 Street, City">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check2-circle me-2"></i>Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="alert alert-info border-0 shadow-sm mt-3 d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Looking to secure your account? Update your password in the Security Settings.
                </div>
                <a href="{{ route('profile.password') }}" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-shield-lock me-1"></i> Change Password
                </a>
            </div>
        </div>
    </div>
</div>
@endsection