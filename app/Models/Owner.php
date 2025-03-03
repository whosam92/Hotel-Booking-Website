<?php


namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // ✅ Correct parent class for authentication
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Authenticatable // ✅ Must extend Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    // An owner can have multiple hotels
    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }
}
