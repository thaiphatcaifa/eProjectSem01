<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Create Cities Table
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 2. Create Articles Table (For News, Diseases, Preventions)
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['news', 'disease', 'prevention']);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // 3. Add missing columns to Users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('password');
            $table->unsignedBigInteger('city_id')->nullable()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('cities');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'city_id', 'phone', 'address']);
        });
    }
};