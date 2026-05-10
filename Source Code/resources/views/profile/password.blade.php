@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-shield-lock text-primary-dark fs-2 me-3"></i>
        <h3 class="text-primary-dark fw-bold mb-0">Security Settings</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary-dark"><i class="bi bi-key me-2"></i>Change Password</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Enter your current password to verify identity" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small">New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="At least 8 characters" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       placeholder="Repeat new password" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-2"></i>Back to Profile
                            </a>
                            <button type="submit" class="btn btn-primary-dark px-4">
                                <i class="bi bi-shield-check me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection