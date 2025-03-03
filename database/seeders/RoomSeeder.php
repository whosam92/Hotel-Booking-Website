<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomSeeder extends Seeder
{
    public function run()
    {
        // Ensure at least one hotel exists before inserting rooms
        $hotel = DB::table('hotels')->first();

        if (!$hotel) {
            $hotelId = DB::table('hotels')->insertGetId([
                'name' => 'Grand Luxury Hotel',
                'description' => 'A luxury hotel with premium services.',
                'location' => 'New York, USA',
                'owner_id' => 1, // Assuming an owner exists
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $hotelId = $hotel->id;
        }

        // Now insert rooms linked to the hotel
        DB::table('rooms')->insert([
            [
                'hotel_id' => $hotelId,
                'name' => 'Deluxe Room',
                'description' => 'A luxurious room with a great view.',
                'price' => 200.00,
                'total_rooms' => 10,
                'amenities' => 'WiFi, TV, AC',
                'size' => '40 sqm',
                'total_beds' => 1,
                'total_bathrooms' => 1,
                'total_balconies' => 1,
                'total_guests' => 2,
                'featured_photo' => 'deluxe_room.jpg',
                'video_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hotel_id' => $hotelId,
                'name' => 'Suite Room',
                'description' => 'A spacious suite with king-size bed.',
                'price' => 350.00,
                'total_rooms' => 5,
                'amenities' => 'WiFi, TV, AC, Mini-Bar',
                'size' => '60 sqm',
                'total_beds' => 1,
                'total_bathrooms' => 2,
                'total_balconies' => 2,
                'total_guests' => 3,
                'featured_photo' => 'suite_room.jpg',
                'video_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
