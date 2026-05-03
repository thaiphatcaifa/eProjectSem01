@extends('layouts.app')
@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <i class="bi bi-search text-primary-dark fs-2 me-3"></i>
            <h3 class="text-primary-dark fw-bold mb-0">Find a Doctor & Book</h3>
        </div>
        
        <form method="GET" action="{{ route('patient.index') }}" class="d-flex w-100 gap-2" style="max-width: 500px;">
            <select name="city_id" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">All Cities</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                @endforeach
            </select>
            <select name="specialty_id" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">All Specialties</option>
                @foreach($specialties as $spec)
                    <option value="{{ $spec->id }}" {{ request('specialty_id') == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('success')) <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div> @endif

    <div class="row g-4">
        @forelse($doctors as $doctor)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                        <div class="bg-primary-light text-primary-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            <img src="{{ $doctor->user->avatar ? asset('uploads/avatars/'.$doctor->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($doctor->user->name) }}" class="rounded-circle" width="60" height="60">
                        </div>
                        <div>
                            <h5 class="card-title text-primary-dark fw-bold mb-1">Dr. {{ $doctor->user->name ?? 'N/A' }}</h5>
                            <span class="badge bg-secondary rounded-pill px-3"><i class="bi bi-star me-1"></i>{{ $doctor->specialty->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <p class="mb-2 text-muted"><i class="bi bi-building me-2 text-primary-dark"></i><strong>Hospital:</strong> {{ $doctor->hospital_name }}</p>
                    <p class="mb-2 text-muted"><i class="bi bi-geo-alt me-2 text-primary-dark"></i><strong>Location:</strong> {{ $doctor->user->city->name ?? 'N/A' }}</p>
                    <p class="mb-4 text-muted"><i class="bi bi-cash-stack me-2 text-primary-dark"></i><strong>Fee:</strong> <span class="text-danger fw-bold fs-5">${{ number_format($doctor->consultation_fee) }}</span></p>
                    
                    <form action="{{ route('patient.book') }}" method="POST" class="bg-light p-3 rounded-3">
                        @csrf
                        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                        <label class="fw-bold mb-2 text-primary-dark small"><i class="bi bi-clock-history me-1"></i> Select Schedule:</label>
                        <select name="schedule_id" class="form-select mb-3 shadow-sm" required>
                            <option value="">-- Available Slots --</option>
                            @foreach($doctor->schedules as $schedule)
                                <option value="{{ $schedule->id }}">
                                    {{ date('M d, Y', strtotime($schedule->date)) }} ({{ $schedule->time_slot }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary-dark w-100"><i class="bi bi-calendar-check me-2"></i>Confirm Booking</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
            <p class="mt-3 text-muted">No doctors found matching your criteria.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection