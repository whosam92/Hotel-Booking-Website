<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Room extends Model
// {
//     use HasFactory;





 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;

        // A room belongs to a hotel
        public function hotel()
        {
            return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
        }
    
    // A room can be booked multiple times
    public function bookedRooms(): HasMany
    {
        return $this->hasMany(BookedRoom::class);
    }

    // A room can have multiple photos
    // public function photos(): HasMany
    // {
    //     return $this->hasMany(RoomPhoto::class);
    // }

    public function rRoomPhoto()
    {
        return $this->hasMany(RoomPhoto::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'room_id');
    }
    
    public function gallery()
    {
        return $this->hasMany(RoomPhoto::class, 'room_id');
    }
    
    
}
