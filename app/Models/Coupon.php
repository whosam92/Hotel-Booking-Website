<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'discount_amount', 'discount_type', 'hotel_id', 'status'
    ];


    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    
}
