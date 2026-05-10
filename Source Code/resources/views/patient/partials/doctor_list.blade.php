@forelse($doctors as $doctor)
<div class="col-md-6 col-lg-4">
    <div class="card h-100 border-0 shadow-sm hover-shadow transition">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <img src="{{ $doctor->user->avatar ? asset('uploads/avatars/'.$doctor->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($doctor->user->name) }}" 
                     class="rounded-circle me-3 border" width="60" height="60" style="object-fit: cover;" alt="Avatar">
                <div>
                    <h5 class="fw-bold mb-1 text-primary-dark">{{ $doctor->user->name }}</h5>
                    <span class="badge bg-light text-primary border">{{ $doctor->specialty->name ?? 'General' }}</span>
                </div>
            </div>
            
            <p class="small text-muted mb-2"><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ $doctor->hospital_name }} - {{ $doctor->user->city->name ?? 'N/A' }}</p>
            
            <form action="{{ route('patient.book') }}" method="POST" class="bg-light p-3 rounded-3 mt-3">
                @csrf
                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                <label class="fw-bold mb-2 text-primary-dark small">
                    <i class="bi bi-clock-history me-1"></i> Select Schedule <span class="text-danger">*</span>
                </label>
                <select name="schedule_id" class="form-select mb-3 shadow-sm" required>
                    <option value="">-- Available Slots --</option>
                    @foreach($doctor->schedules as $schedule)
                        <option value="{{ $schedule->id }}">
                            {{ date('M d, Y', strtotime($schedule->date)) }} ({{ $schedule->time_slot }}) 
                            - {{ number_format($schedule->price ?? $doctor->consultation_fee) }} VND
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-calendar-check me-2"></i>Confirm Booking
                </button>
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