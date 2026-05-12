@extends('layouts.app')
@section('content')
@php
    use Carbon\Carbon;
@endphp
<div class="container">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-speedometer2 text-primary-dark fs-2 me-3"></i>
        <h3 class="text-primary-dark fw-bold mb-0">Doctor Dashboard</h3>
    </div>
    
    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div> 
    @endif
    @if(session('error')) 
        <div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div> 
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card h-100 shadow-sm border-0 hover-pop transition">
                <div class="card-header bg-primary-dark text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle icon-thin me-2"></i>Post Availability</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('doctor.schedule.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="text-muted small fw-bold mb-1"><i class="bi bi-calendar-event me-1"></i> Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold mb-1"><i class="bi bi-clock me-1"></i> Time Slot <span class="text-danger">*</span></label>
                            <input type="text" name="time_slot" class="form-control" placeholder="e.g. 08:00 - 09:00 AM" required>
                        </div>
                        <div class="mb-4">
                            <label class="text-muted small fw-bold mb-1"><i class="bi bi-cash me-1"></i> Consultation Fee (VND) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" value="{{ Auth::user()->doctor->consultation_fee ?? 500000 }}" min="0" required>
                        </div>
                        <button type="submit" class="btn btn-primary-dark w-100 fw-bold py-2 shadow-sm">Publish Schedule</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card mb-4 shadow-sm border-0 hover-pop transition">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="text-primary-dark mb-0 fw-bold"><i class="bi bi-calendar-check icon-thin me-2"></i>Patient Appointments</h5>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Patient</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $app)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $app->patient->name ?? 'Unknown' }}</td>
                                <td>
                                    {{ date('d/m/Y', strtotime($app->schedule->date)) }}<br>
                                    <small class="text-muted">{{ $app->schedule->time_slot }}</small>
                                </td>
                                <td>
                                    @if($app->status == 'Pending')
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending</span>
                                    @elseif($app->status == 'Confirmed')
                                        <span class="badge bg-success rounded-pill px-3 py-2">Confirmed</span>
                                    @elseif($app->status == 'Cancelled')
                                        <span class="badge bg-danger rounded-pill px-3 py-2">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">{{ $app->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($app->status == 'Pending')
                                        <form action="{{ route('doctor.appointment.confirm', $app->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success shadow-sm" title="Confirm Appointment">
                                                <i class="bi bi-check-lg"></i> Confirm
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $app->id }}" title="Cancel Appointment">
                                            <i class="bi bi-x-lg"></i> Cancel
                                        </button>
                                    @else
                                        <span class="text-muted small">No actions</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No appointments booked yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm border-0 hover-pop transition">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="text-primary-dark mb-0 fw-bold"><i class="bi bi-card-list icon-thin me-2"></i>Your Posted Schedules</h5>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Time Slot</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $s)
                            @php
                                $timeStartRaw = trim(explode('-', $s->time_slot)[0]);
                                if (preg_match('/(AM|PM)/i', $timeStartRaw) && (int)substr($timeStartRaw, 0, 2) >= 13) {
                                    $timeStartRaw = trim(str_ireplace(['AM', 'PM'], '', $timeStartRaw));
                                }
                                try {
                                    $isExpired = \Carbon\Carbon::parse($s->date . ' ' . $timeStartRaw, 'Asia/Ho_Chi_Minh')->isPast();
                                } catch (\Exception $e) {
                                    $isExpired = false;
                                }
                            @endphp
                            <tr>
                                <td class="ps-4">{{ date('d/m/Y', strtotime($s->date)) }}</td>
                                <td>{{ $s->time_slot }}</td>
                                <td>{{ number_format($s->price) }} VND</td>
                                <td>
                                    @if($s->is_booked) 
                                        <span class="badge bg-danger rounded-pill px-3 py-2">Booked</span>
                                    @elseif($isExpired)
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">Expired</span>
                                    @else 
                                        <span class="badge bg-success rounded-pill px-3 py-2">Available</span> 
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if(!$s->is_booked && !$isExpired)
                                        <button type="button" class="btn btn-sm btn-outline-primary me-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#editScheduleModal{{ $s->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <form action="{{ route('doctor.schedule.destroy', $s->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" onclick="return confirm('Delete this schedule?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Locked</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($appointments as $app)
    @if($app->status == 'Pending')
        <div class="modal fade text-start" id="cancelModal{{ $app->id }}" tabindex="-1" aria-labelledby="cancelModalLabel{{ $app->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('doctor.appointment.cancel', $app->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="cancelModalLabel{{ $app->id }}">Cancel Appointment</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to cancel the appointment with <strong>{{ $app->patient->name }}</strong>?</p>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Reason for Cancellation <span class="text-danger">*</span></label>
                                <textarea name="cancel_reason" class="form-control" rows="3" placeholder="Explain why you are cancelling this appointment..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger px-4">Confirm Cancellation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@foreach($schedules as $s)
    @php
        $timeStartRaw = trim(explode('-', $s->time_slot)[0]);
        if (preg_match('/(AM|PM)/i', $timeStartRaw) && (int)substr($timeStartRaw, 0, 2) >= 13) {
            $timeStartRaw = trim(str_ireplace(['AM', 'PM'], '', $timeStartRaw));
        }
        try {
            $isExpired = \Carbon\Carbon::parse($s->date . ' ' . $timeStartRaw, 'Asia/Ho_Chi_Minh')->isPast();
        } catch (\Exception $e) {
            $isExpired = false;
        }
    @endphp
    @if(!$s->is_booked && !$isExpired)
        <div class="modal fade text-start" id="editScheduleModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('doctor.schedule.update', $s->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Edit Schedule</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ $s->date }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Time Slot <span class="text-danger">*</span></label>
                                <input type="text" name="time_slot" class="form-control" value="{{ $s->time_slot }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Price (VND) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" value="{{ $s->price }}" min="0" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection