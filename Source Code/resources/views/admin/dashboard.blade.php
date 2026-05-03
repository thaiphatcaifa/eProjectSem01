@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">System Administrator Dashboard</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#cities">City Management</a>
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
        
        <div class="tab-pane fade show active" id="cities">
            <div class="card">
                <div class="card-header bg-dark text-white">Add & Manage Cities</div>
                <div class="card-body">
                    <form action="{{ route('admin.cities.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="Enter New City Name" required>
                            <button class="btn btn-primary" type="submit">Add City</button>
                        </div>
                    </form>
                    <table class="table table-bordered">
                        <thead><tr><th>ID</th><th>City Name</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($cities as $city)
                            <tr>
                                <td>{{ $city->id }}</td>
                                <td>{{ $city->name }}</td>
                                <td>
                                    <form action="{{ route('admin.cities.destroy', $city->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this city?')">Delete</button>
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
            <div class="card">
                <div class="card-header bg-dark text-white">User Accounts & Patient Records</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge bg-secondary">{{ strtoupper($user->role) }}</span></td>
                                <td>
                                    @if($user->role == 'deactivated') <span class="badge bg-danger">Inactive</span>
                                    @else <span class="badge bg-success">Active</span> @endif
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
            <div class="card">
                <div class="card-header bg-dark text-white">Doctor Details</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead><tr><th>Doc ID</th><th>Name</th><th>Specialty</th><th>Bio</th></tr></thead>
                        <tbody>
                            @foreach($doctors as $doctor)
                            <tr>
                                <td>{{ $doctor->id }}</td>
                                <td>{{ $doctor->user->name ?? 'N/A' }}</td>
                                <td>{{ $doctor->specialty->name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($doctor->bio, 50) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="content">
            <div class="card">
                <div class="card-header bg-dark text-white">Publish Diseases, Cures & News</div>
                <div class="card-body">
                    <form action="{{ route('admin.articles.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-8 mb-2">
                                <input type="text" name="title" class="form-control" placeholder="Article Title" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <select name="type" class="form-control" required>
                                    <option value="news">Medical News</option>
                                    <option value="disease">Disease Information</option>
                                    <option value="prevention">Prevention & Cure</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <textarea name="content" class="form-control" rows="3" placeholder="Article Content..." required></textarea>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary" type="submit">Publish Content</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <table class="table table-sm">
                        <thead><tr><th>Title</th><th>Type</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($articles as $article)
                            <tr>
                                <td>{{ $article->title }}</td>
                                <td><span class="badge bg-info text-dark">{{ strtoupper($article->type) }}</span></td>
                                <td>
                                    <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this article?')">Delete</button>
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