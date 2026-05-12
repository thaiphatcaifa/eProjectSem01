@extends('layouts.app')
@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <i class="bi bi-search text-primary-dark fs-2 me-3"></i>
            <h3 class="text-primary-dark fw-bold mb-0">Find a Doctor & Book</h3>
        </div>
        
        <form id="searchDoctorForm" class="d-flex w-100 gap-2" style="max-width: 700px;" onsubmit="return false;">
            <input type="text" id="searchName" class="form-control shadow-sm" placeholder="Search doctor by name...">
            
            <select id="city_id" class="form-select shadow-sm">
                <option value="">All Cities</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                @endforeach
            </select>
            
            <select id="specialty_id" class="form-select shadow-sm">
                <option value="">All Specialties</option>
                @foreach($specialties as $spec)
                    <option value="{{ $spec->id }}" {{ request('specialty_id') == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div> 
    @endif
    @if(session('error')) 
        <div class="alert alert-danger border-0 shadow-sm">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        </div> 
    @endif

    <div class="row g-4" id="doctors-container">
        @include('patient.partials.doctor_list')
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchName = document.getElementById('searchName');
        const citySelect = document.getElementById('city_id');
        const specialtySelect = document.getElementById('specialty_id');
        const doctorsContainer = document.getElementById('doctors-container');

        // Hàm gọi API lấy danh sách
        function fetchDoctors() {
            const search = searchName.value;
            const city = citySelect.value;
            const specialty = specialtySelect.value;

            // Truyền params lên backend
            const url = `{{ route('patient.index') }}?search=${encodeURIComponent(search)}&city_id=${encodeURIComponent(city)}&specialty_id=${encodeURIComponent(specialty)}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Khai báo đây là request AJAX
                }
            })
            .then(response => response.text()) // FIX LỖI: Đổi từ json() sang text() vì backend đang trả về chuỗi HTML
            .then(html => {
                // FIX LỖI: Gán trực tiếp chuỗi HTML nhận được vào giao diện
                doctorsContainer.innerHTML = html;
            })
            .catch(error => console.error('Error fetching doctors:', error));
        }

        // Kích hoạt tìm kiếm tự động khi người dùng thao tác
        searchName.addEventListener('keyup', fetchDoctors);
        citySelect.addEventListener('change', fetchDoctors);
        specialtySelect.addEventListener('change', fetchDoctors);
    });
</script>
@endsection