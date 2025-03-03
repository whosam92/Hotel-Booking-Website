<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;

// class Customer extends Authenticatable
// {
//     use HasFactory;
// }


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Authenticatable
{
    use HasFactory;

    // A customer can have many orders
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
{
    return $this->hasMany(Review::class, 'customer_id');
}

}
