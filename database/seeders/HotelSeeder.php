<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HotelSeeder extends Seeder
{
    public function run()
    {
        DB::table('hotels')->insert([
            [
                'name' => 'Grand Luxury Hotel',
                'description' => 'A luxury hotel with premium services.',
                'location' => 'New York, USA',
                'owner_id' => 1, // Assuming the first owner exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Seaside Resort',
                'description' => 'A beachfront resort with ocean views.',
                'location' => 'Miami, USA',
                'owner_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
