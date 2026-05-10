<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\City;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use App\Models\Article;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. TẠO TÀI KHOẢN ADMIN MẶC ĐỊNH
        User::updateOrCreate(
            ['email' => 'admin@mediconnect.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'phone' => '0900000000',
                'address' => 'MediConnect HQ'
            ]
        );

        // 2. TẠO DỮ LIỆU THÀNH PHỐ (CITIES)
        $cities = ['Ho Chi Minh City', 'Hanoi', 'Da Nang', 'Can Tho', 'Hai Phong'];
        foreach ($cities as $cityName) {
            City::firstOrCreate(['name' => $cityName]);
        }

        // 3. TẠO DỮ LIỆU CHUYÊN KHOA (SPECIALTIES)
        $specialties = ['Cardiology (Tim mạch)', 'Dermatology (Da liễu)', 'Neurology (Thần kinh)', 'Pediatrics (Nhi khoa)', 'Dentistry (Nha khoa)'];
        foreach ($specialties as $specName) {
            Specialty::firstOrCreate(['name' => $specName]);
        }

        // 4. TẠO TÀI KHOẢN BÁC SĨ & HỒ SƠ BÁC SĨ (Kịch bản Demo: 3 Bác sĩ)
        $doctorData = [
            ['name' => 'Dr. John Smith', 'email' => 'john@doctor.com', 'specialty_id' => 1, 'city_id' => 1],
            ['name' => 'Dr. Sarah Connor', 'email' => 'sarah@doctor.com', 'specialty_id' => 2, 'city_id' => 2],
            ['name' => 'Dr. Michael Bay', 'email' => 'michael@doctor.com', 'specialty_id' => 3, 'city_id' => 1],
        ];

        $doctors = [];
        foreach ($doctorData as $index => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('12345678'),
                    'role' => 'doctor',
                    'phone' => '091000000' . $index,
                    'city_id' => $data['city_id']
                ]
            );

            $doctors[] = Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialty_id' => $data['specialty_id'],
                    'hospital_name' => 'MediConnect General Hospital',
                    'consultation_fee' => 500000
                ]
            );
        }

        // 5. TẠO TÀI KHOẢN BỆNH NHÂN (Kịch bản Demo: 2 Bệnh nhân)
        $patientUsers = [];
        for ($i = 1; $i <= 2; $i++) {
            $patientUsers[] = User::firstOrCreate(
                ['email' => "patient{$i}@mail.com"],
                [
                    'name' => "Test Patient {$i}",
                    'password' => Hash::make('12345678'),
                    'role' => 'patient',
                    'phone' => "092000000{$i}",
                    'city_id' => 1
                ]
            );
        }

        // 6. TẠO LỊCH TRỰC VÀ CUỘC HẸN DEMO CÓ SẴN
        $now = Carbon::now();
        
        // Bác sĩ 1 có 3 lịch (1 Trống, 1 Pending, 1 Confirmed)
        $doc1 = $doctors[0];
        
        // Slot 1: Trống (Bệnh nhân có thể book vào đây)
        DoctorSchedule::create([
            'doctor_id' => $doc1->id,
            'date' => $now->copy()->addDays(2)->format('Y-m-d'),
            'time_slot' => '08:00 - 09:00',
            'price' => 500000,
            'is_booked' => false
        ]);

        // Slot 2: Đã có bệnh nhân book, đang chờ duyệt (Pending)
        $schedule2 = DoctorSchedule::create([
            'doctor_id' => $doc1->id,
            'date' => $now->copy()->addDays(3)->format('Y-m-d'),
            'time_slot' => '09:00 - 10:00',
            'price' => 500000,
            'is_booked' => true
        ]);
        Appointment::create([
            'patient_id' => $patientUsers[0]->id,
            'doctor_id' => $doc1->id,
            'schedule_id' => $schedule2->id,
            'status' => 'Pending'
        ]);

        // Slot 3: Đã được bác sĩ duyệt (Confirmed)
        $schedule3 = DoctorSchedule::create([
            'doctor_id' => $doc1->id,
            'date' => $now->copy()->addDays(4)->format('Y-m-d'),
            'time_slot' => '14:00 - 15:00',
            'price' => 600000, // Giá khác biệt
            'is_booked' => true
        ]);
        Appointment::create([
            'patient_id' => $patientUsers[1]->id,
            'doctor_id' => $doc1->id,
            'schedule_id' => $schedule3->id,
            'status' => 'Confirmed'
        ]);

        // 7. TẠO BÀI VIẾT (TIN TỨC)
        Article::firstOrCreate(
            ['title' => 'How to maintain a healthy heart'],
            [
                'content' => 'Regular exercise, balanced diet, and stress management are key to cardiovascular health...',
                'type' => 'prevention'
            ]
        );
        Article::firstOrCreate(
            ['title' => 'MediConnect launches new booking system'],
            [
                'content' => 'We are proud to announce our upgraded fast and secure online appointment booking platform...',
                'type' => 'news'
            ]
        );
    }
}