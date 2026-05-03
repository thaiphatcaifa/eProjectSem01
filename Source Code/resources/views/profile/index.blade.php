@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Profile Management</h2>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img src="{{ Auth::user()->avatar ? asset('uploads/avatars/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" class="rounded-circle mb-3" width="150" height="150" alt="Avatar">
                    <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="avatar" class="form-control mb-2" accept="image/*" required>
                        <button type="submit" class="btn btn-sm btn-primary w-100">Upload Profile Picture</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><strong>Personal Information</strong></div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Email Address (Read-only)</label>
                            <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ Auth::user()->phone }}">
                        </div>
                        <div class="mb-3">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" value="{{ Auth::user()->address }}">
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label>New Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection