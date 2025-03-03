<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class RoomPhoto extends Model
// {
//     use HasFactory;
// }



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPhoto extends Model
{
    use HasFactory;

    // ✅ Allow mass assignment for these fields
    protected $fillable = ['room_id', 'photo'];

    // Define relationship with Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
