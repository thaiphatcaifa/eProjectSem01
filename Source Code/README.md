# MediConnect Group

=======================================
+ **Supervisor**: LÊ THANH NHÂN
+ **Semester**: 1
+ **Batch No**: T3.2511.MO
+ **Group No**: 3
+ **List Of Member**:
1. THÁI PHÁT (Student1693582) - Team Leader
2. LA VĨ QUYỀN (Student1693584)
3. TRƯƠNG KIM NGÂN (Student1690424)
4. HUỲNH ANH (Student1693580)
5. TRẦN GIÀU (Student1700913)

=======================================

## Tính năng chính

### Đối với Bệnh nhân:
- Đăng ký, đăng nhập và quản lý hồ sơ cá nhân (Avatar, thông tin liên lạc).
- Tìm kiếm bác sĩ theo Chuyên khoa và Thành phố.
- Xem lịch trống, đặt lịch hẹn trực tuyến và quản lý lịch sử khám.

### Đối với Bác sĩ:
- Tự chủ quản lý lịch làm việc và các ca trống (time slots).
- Dashboard quản lý, xác nhận hoặc hủy danh sách bệnh nhân đặt lịch.

### Đối với Quản trị viên (Admin):
- Quản lý danh mục chuyên khoa, thành phố và tài khoản người dùng.
- Quản lý bài viết tin tức và kiến thức phòng bệnh y tế.
- Xem báo cáo thống kê hoạt động của hệ thống.

## Công nghệ sử dụng

- **Backend**: PHP 8.2+, Laravel Framework 11.x.
- **Frontend**: Blade Template, Bootstrap 5.3, Vite.
- **Database**: MySQL 8.0 / SQLite.
- **Tools**: Composer, NPM, Git, VS Code.

## Cấu trúc dự án

- `/Source Code`: Toàn bộ mã nguồn ứng dụng Laravel.
- `/Documentations`: Tài liệu đặc tả (CRS), thiết kế (DFD, ERD) và hướng dẫn.

## Hướng dẫn cài đặt và Khởi chạy (Local)

Thực hiện các bước sau để chạy dự án trên máy cục bộ (sử dụng XAMPP hoặc môi trường tương đương):

1. **Tải mã nguồn**:
   ```bash
   git clone [https://github.com/thaiphatcaifa/eProjectSem01.git](https://github.com/thaiphatcaifa/eProjectSem01.git)
   cd "eProjectSem01/Source Code"
   ```

2. **Cài đặt các thư viện phụ thuộc**:
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Thiết lập cấu hình**:
   - Sao chép file mẫu: `cp .env.example .env`
   - Tạo mã bảo mật: `php artisan key:generate`
   - Cấu hình Database trong file `.env` (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

4. **Khởi tạo dữ liệu**:
   ```bash
   php artisan migrate --seed
   ```

5. **Khởi chạy ứng dụng**:
   ```bash
   php artisan serve
   ```
   Sau đó truy cập: `http://127.0.0.1:8000`

=======================================

**Thank you for visiting our project!**
