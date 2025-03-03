<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class BookedRoom extends Model
// {
//     use HasFactory;
// }



 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookedRoom extends Model
{
    use HasFactory;

    // A booking belongs to a room
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
