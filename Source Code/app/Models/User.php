<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use Notifiable;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'city_id', 
        'phone',   
        'address', 
        'avatar',  
    ];
    
    protected $hidden = [
        'password', 
        'remember_token'
    ];
    
    public function doctor() { 
        return $this->hasOne(Doctor::class); 
    }

    // Add this missing relationship
    public function city() {
        return $this->belongsTo(City::class);
    }
}