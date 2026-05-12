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

        let timeoutId;
        let abortController;
        let isInteracting = false;

        // 1. FIX GIẬT LAG DROPDOWN: Tạm dừng auto-refresh khi người dùng rê chuột hoặc tương tác với danh sách
        doctorsContainer.addEventListener('mouseenter', () => isInteracting = true);
        doctorsContainer.addEventListener('mouseleave', () => isInteracting = false);
        doctorsContainer.addEventListener('focusin', () => isInteracting = true);
        doctorsContainer.addEventListener('focusout', () => isInteracting = false);

        function fetchDoctors() {
            // 2. FIX RACE CONDITION: Hủy request cũ đang bay trên mạng nếu có request mới phát sinh
            if (abortController) {
                abortController.abort();
            }
            abortController = new AbortController();

            const search = searchName.value;
            const city = citySelect.value;
            const specialty = specialtySelect.value;

            const url = `{{ route('patient.index') }}?search=${encodeURIComponent(search)}&city_id=${encodeURIComponent(city)}&specialty_id=${encodeURIComponent(specialty)}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                signal: abortController.signal
            })
            .then(response => {
                if (!response.ok) throw new Error('Lỗi kết nối');
                return response.text(); // 3. FIX LỖI KHÔNG HIỆN BÁC SĨ: Đổi json() thành text()
            })
            .then(html => {
                doctorsContainer.innerHTML = html; // Render HTML thẳng vào vùng chứa
            })
            .catch(error => {
                // Chỉ văng log lỗi nếu lỗi đó KHÔNG PHẢI là do chúng ta chủ động hủy (AbortError)
                if (error.name !== 'AbortError') {
                    console.error('Error fetching doctors:', error);
                }
            });
        }

        // 4. KỸ THUẬT DEBOUNCE: Chờ người dùng ngừng gõ phím 500ms thì mới gửi truy vấn
        function debouncedFetch() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                fetchDoctors();
            }, 500);
        }

        // Lắng nghe sự kiện (Dùng 'input' thay vì 'keyup' để bắt được cả hành vi bôi đen paste chuột)
        searchName.addEventListener('input', debouncedFetch);
        citySelect.addEventListener('change', fetchDoctors);
        specialtySelect.addEventListener('change', fetchDoctors);

        // Kích hoạt auto-refresh mỗi 15 giây (NHƯNG BỎ QUA nếu người dùng đang dùng chuột trên thẻ lịch)
        setInterval(() => {
            if (!isInteracting) {
                fetchDoctors();
            }
        }, 15000);
    });
</script>
@endsection