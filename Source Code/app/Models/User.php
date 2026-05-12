<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'city_id',
        'phone',
        'address',
        'avatar',
        'is_requesting_doctor',
        'is_active', // Bổ sung trường này để cho phép cập nhật trạng thái tài khoản
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean', // Cast về kiểu boolean để dễ xử lý logic
        ];
    }

    /**
     * Mối quan hệ với bảng City
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Mối quan hệ với bảng Doctor
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }
}