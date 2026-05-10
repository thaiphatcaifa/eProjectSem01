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
            <div class="card border-0 shadow-sm mb-4">
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
                    <div class="card border-0 shadow-sm bg-primary text-white h-100">
                        <div class="card-body text-center p-4">
                            <h6 class="text-uppercase fw-bold mb-3">Total Appointments</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ $totalAppointments ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-success text-white h-100">
                        <div class="card-body text-center p-4">
                            <h6 class="text-uppercase fw-bold mb-3">Completed</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ $completedAppointments ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-danger text-white h-100">
                        <div class="card-body text-center p-4">
                            <h6 class="text-uppercase fw-bold mb-3">Cancelled</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ $cancelledAppointments ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-warning text-dark h-100">
                        <div class="card-body text-center p-4">
                            <h6 class="text-uppercase fw-bold mb-3">Pending</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ $pendingAppointments ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm bg-info text-white h-100">
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
                    <div class="card border-0 shadow-sm bg-secondary text-white h-100">
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
            <div class="card border-0 shadow-sm">
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
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this city?')"><i class="bi bi-trash"></i> Delete</button>
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="mb-0">User Accounts Overview</h6>
                </div>
                <div class="card-body p-4 table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $user->role == 'admin' ? 'bg-danger' : ($user->role == 'doctor' ? 'bg-success' : 'bg-primary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->role != 'admin')
                                    <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm {{ $user->role == 'deactivated' ? 'btn-success' : 'btn-warning' }}">
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="mb-0">Registered Doctors</h6>
                </div>
                <div class="card-body p-4 table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Doctor Name</th><th>Specialty</th><th>Hospital</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($doctors as $doctor)
                            <tr>
                                <td>{{ $doctor->user->name ?? 'N/A' }}</td>
                                <td>{{ $doctor->specialty->name ?? 'N/A' }}</td>
                                <td>{{ $doctor->hospital_name }}</td>
                                <td>
                                    <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this doctor profile?')"><i class="bi bi-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="content">
            <div class="card border-0 shadow-sm">
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
                    <table class="table table-hover">
                        <thead><tr><th>Title</th><th>Type</th><th class="text-end">Actions</th></tr></thead>
                        <tbody>
                            @foreach($articles as $article)
                            <tr>
                                <td>{{ $article->title }}</td>
                                <td><span class="badge bg-info text-dark">{{ strtoupper($article->type) }}</span></td>
                                <td class="text-end">
                                    <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this article?')"><i class="bi bi-trash"></i></button>
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
@endsection