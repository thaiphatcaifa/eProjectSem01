<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * File này bổ sung cột price để bác sĩ có thể nhập giá khám 
     * cho từng ca trực cụ thể theo góp ý của giảng viên.
     */
    public function up(): void
    {
        Schema::table('doctor_schedules', function (Blueprint $table) {
            // Thêm cột price sau cột time_slot, mặc định là 0
            $table->decimal('price', 15, 2)->after('time_slot')->default(0)->comment('Consultation fee for this specific slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};