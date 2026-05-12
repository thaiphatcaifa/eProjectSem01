@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-grid-fill text-primary-dark fs-2 me-3"></i>
        <h2 class="mb-0 text-primary-dark fw-bold">System Administrator Dashboard</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
    @endif

    <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#statistics">
                <i class="bi bi-bar-chart-fill me-1"></i> Statistics & Reports
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#cities">City Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#specialties">Specialties</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#users">User & Patient Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#doctors">Doctor Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#content">Content Management</a>
        </li>
    </ul>

    <div class="tab-content" id="adminTabsContent">
        
        <div class="tab-pane fade show active" id="statistics">
            <div class="card border-0 shadow-sm mb-4 hover-pop transition">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-funnel me-2"></i>Filter Data by Date</h5>
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Start Date</label>
                            <input type="date" name="start_date" class="form-control shadow-sm" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">End Date</label>
                            <input type="date" name="end_date" class="form-control shadow-sm" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary shadow-sm"><i class="bi bi-search me-2"></i>Filter</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary shadow-sm">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-primary text-white h-100 hover-pop transition">
                        <div class="card-body text-center p-4">
                            <h6 class="text-uppercase fw-bold mb-3">Total Appointments</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ $totalAppointments ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-success text-white h-100 hover-pop transition">
                        <div class="card-body text-center p-4">
                            <h6 class="text-uppercase fw-bold mb-3">Completed</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ $completedAppointments ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-danger text-white h-100 hover-pop transition">
                        <div class="card-body text-center p-4">
                            <h6 class="text-uppercase fw-bold mb-3">Cancelled</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ $cancelledAppointments ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-warning text-dark h-100 hover-pop transition">
                        <div class="card-body text-center p-4">
                            <h6 class="text-uppercase fw-bold mb-3">Pending</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ $pendingAppointments ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm bg-info text-white h-100 hover-pop transition">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-1">Total Patients Registered</h6>
                                <h2 class="fw-bold mb-0">{{ $totalPatients ?? 0 }}</h2>
                            </div>
                            <i class="bi bi-people-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm bg-secondary text-white h-100 hover-pop transition">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-1">Total Doctors Active</h6>
                                <h2 class="fw-bold mb-0">{{ $totalDoctors ?? 0 }}</h2>
                            </div>
                            <i class="bi bi-heart-pulse-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="cities">
            <div class="card border-0 shadow-sm hover-pop transition">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="mb-0">Add & Manage Cities</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.cities.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="Enter new city name..." required>
                            <button class="btn btn-primary" type="submit">Add City</button>
                        </div>
                    </form>
                    <hr>
                    <table class="table table-hover">
                        <thead><tr><th>City Name</th><th class="text-end">Actions</th></tr></thead>
                        <tbody>
                            @foreach($cities as $city)
                            <tr>
                                <td>{{ $city->name }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.cities.destroy', $city->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Delete this city?')"><i class="bi bi-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="specialties">
            <div class="card border-0 shadow-sm hover-pop transition">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="mb-0">Add & Manage Specialties</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.specialties.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="Enter new specialty name..." required>
                            <button class="btn btn-primary" type="submit">Add Specialty</button>
                        </div>
                    </form>
                    <hr>
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Specialty Name</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($specialties as $spec)
                            <tr>
                                <td class="fw-bold">{{ $spec->name }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.specialties.destroy', $spec->id) }}" method="POST">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Delete this specialty?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="users">
            <div class="card border-0 shadow-sm hover-pop transition">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="mb-0">User Accounts Overview</h6>
                </div>
                <div class="card-body p-4 table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    {{ $user->name }}
                                    @if($user->is_requesting_doctor && ($user->role == 'patient' || $user->role == 1))
                                        <span class="badge bg-warning text-dark ms-2"><i class="bi bi-star-fill me-1"></i>Pending Doctor</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $user->role == 'admin' ? 'bg-danger' : ($user->role == 'doctor' || $user->role == 2 ? 'bg-success' : 'bg-primary') }}">
                                        @if($user->role == 'doctor' || $user->role == 2) Doctor 
                                        @elseif($user->role == 'admin') Admin 
                                        @else Patient @endif
                                    </span>
                                </td>
                                <td>
                                    @if($user->is_requesting_doctor && ($user->role == 'patient' || $user->role == 1))
                                        <button type="button" class="btn btn-sm btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#upgradeModal{{ $user->id }}">
                                            <i class="bi bi-person-badge"></i> Approve Doctor
                                        </button>
                                    @elseif($user->role != 'admin')
                                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm shadow-sm {{ $user->role == 'deactivated' ? 'btn-success' : 'btn-warning' }}">
                                                {{ $user->role == 'deactivated' ? 'Activate' : 'Deactivate' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="doctors">
            <div class="card border-0 shadow-sm hover-pop transition">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="mb-0">Registered Doctors</h6>
                </div>
                <div class="card-body p-4 table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th>Specialty</th>
                                <th>Hospital</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doctors as $docUser)
                            <tr>
                                <td class="fw-bold">{{ $docUser->name }}</td>
                                <td>
                                    @if($docUser->doctor && $docUser->doctor->specialty)
                                        <span class="badge bg-info text-dark">{{ $docUser->doctor->specialty->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Chưa cập nhật hồ sơ</span>
                                    @endif
                                </td>
                                <td>{{ $docUser->doctor->hospital_name ?? 'N/A' }}</td>
                                <td class="text-end">
                                    @if($docUser->doctor)
                                        <form action="{{ route('admin.doctors.destroy', $docUser->doctor->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Delete this doctor profile? The user will be demoted back to a Patient (Role 1).')">
                                                <i class="bi bi-trash"></i> Demote to Patient
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">No profile record</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="content">
            <div class="card border-0 shadow-sm hover-pop transition">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="mb-0">Publish Medical Articles</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.articles.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <input type="text" name="title" class="form-control" placeholder="Article Title" required>
                            </div>
                            <div class="col-md-4">
                                <select name="type" class="form-select" required>
                                    <option value="news">News</option>
                                    <option value="disease">Disease Info</option>
                                    <option value="prevention">Prevention Tips</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <textarea name="content" class="form-control" rows="3" placeholder="Article Content..." required></textarea>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-send me-1"></i> Publish Content</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Title</th><th>Type</th><th class="text-end">Actions</th></tr></thead>
                        <tbody>
                            @foreach($articles as $article)
                            <tr>
                                <td>{{ $article->title }}</td>
                                <td><span class="badge bg-info text-dark">{{ strtoupper($article->type) }}</span></td>
                                <td class="text-end">
                                    <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Delete this article?')"><i class="bi bi-trash"></i></button>
                                    </form>
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

@foreach($users as $user)
    @if($user->is_requesting_doctor && ($user->role == 'patient' || $user->role == 1))
        <div class="modal fade" id="upgradeModal{{ $user->id }}" tabindex="-1" aria-labelledby="upgradeModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('admin.users.upgrade', $user->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="upgradeModalLabel{{ $user->id }}">Upgrade {{ $user->name }} to Doctor</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Specialty <span class="text-danger">*</span></label>
                                <select name="specialty_id" class="form-select shadow-sm" required>
                                    <option value="">-- Choose Specialty --</option>
                                    @foreach($specialties as $spec)
                                        <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select City <span class="text-danger">*</span></label>
                                <select name="city_id" class="form-select shadow-sm" required>
                                    <option value="">-- Choose City --</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hospital/Clinic Name <span class="text-danger">*</span></label>
                                <input type="text" name="hospital_name" class="form-control shadow-sm" placeholder="e.g. City General Hospital" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Base Consultation Fee (VND) <span class="text-danger">*</span></label>
                                <input type="number" name="consultation_fee" class="form-control shadow-sm" value="500000" min="0" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success px-4 shadow-sm">Confirm & Upgrade</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection