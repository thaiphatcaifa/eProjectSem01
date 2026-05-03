@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">My Appointments</h2>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="card shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Doctor</th>
                        <th>Hospital</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $app)
                    <tr>
                        <td class="ps-4 fw-bold">Dr. {{ $app->doctor->user->name ?? 'N/A' }}</td>
                        <td>{{ $app->doctor->hospital_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($app->schedule->date)->format('M d, Y') }} ({{ $app->schedule->time_slot }})</td>
                        <td>
                            @if($app->status == 'Confirmed') <span class="badge bg-success">Confirmed</span>
                            @elseif($app->status == 'Cancelled') <span class="badge bg-danger">Cancelled</span>
                            @else <span class="badge bg-warning text-dark">{{ $app->status }}</span> @endif
                        </td>
                        <td>
                            @if($app->status != 'Cancelled')
                            <form action="{{ route('patient.cancel', $app->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">Cancel Booking</button>
                            </form>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>Cancelled</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection