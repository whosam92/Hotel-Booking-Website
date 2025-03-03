<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'location', 'owner_id'];

    // A hotel belongs to an owner
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    // A hotel has many rooms
    public function rooms()
    {
        return $this->hasMany(Room::class, 'hotel_id', 'id');
    }
}
