<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            CustomerSeeder::class,
            OwnerSeeder::class,
            HotelSeeder::class,
            RoomSeeder::class,
            BookedRoomSeeder::class,
            OrderSeeder::class,
            OrderDetailSeeder::class,
        ]);
    }
}

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('admins')->insert([
            ['name' => 'HUSSAM ADMIN', 'email' => 'hussam@admin.com', 'password' => Hash::make('Aa@12345'), 'photo' => 'admin.jpg', 'token' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

class CustomerSeeder extends Seeder
{
    public function run()
    {
        DB::table('customers')->insert([
            ['name' => 'John Doe', 'email' => 'johndoe@example.com', 'password' => Hash::make('password123'), 'phone' => '123456789', 'country' => 'USA', 'address' => '123 Main St', 'state' => 'CA', 'city' => 'Los Angeles', 'zip' => '90001', 'photo' => 'default.jpg', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

class OwnerSeeder extends Seeder
{
    public function run()
    {
        DB::table('owners')->insert([
            ['name' => 'Hotel Owner One', 'email' => 'owner1@example.com', 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hotel Owner Two', 'email' => 'owner2@example.com', 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

class HotelSeeder extends Seeder
{
    public function run()
    {
        DB::table('hotels')->insert([
            ['name' => 'Grand Luxury Hotel', 'description' => 'A luxury hotel with premium services.', 'location' => 'New York, USA', 'owner_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Seaside Resort', 'description' => 'A beachfront resort with ocean views.', 'location' => 'Miami, USA', 'owner_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

class RoomSeeder extends Seeder
{
    public function run()
    {
        $hotel = DB::table('hotels')->first();
        $hotelId = $hotel ? $hotel->id : 1;

        DB::table('rooms')->insert([
            ['hotel_id' => $hotelId, 'name' => 'Deluxe Room', 'description' => 'A luxurious room with a great view.', 'price' => 200.00, 'total_rooms' => 10, 'amenities' => 'WiFi, TV, AC', 'size' => '40 sqm', 'total_beds' => 1, 'total_bathrooms' => 1, 'total_balconies' => 1, 'total_guests' => 2, 'featured_photo' => 'deluxe_room.jpg', 'video_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['hotel_id' => $hotelId, 'name' => 'Suite Room', 'description' => 'A spacious suite with king-size bed.', 'price' => 350.00, 'total_rooms' => 5, 'amenities' => 'WiFi, TV, AC, Mini-Bar', 'size' => '60 sqm', 'total_beds' => 1, 'total_bathrooms' => 2, 'total_balconies' => 2, 'total_guests' => 3, 'featured_photo' => 'suite_room.jpg', 'video_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

class BookedRoomSeeder extends Seeder
{
    public function run()
    {
        DB::table('booked_rooms')->insert([
            ['booking_date' => Carbon::now(), 'order_no' => 'ORD12345', 'room_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

class OrderSeeder extends Seeder
{
    public function run()
    {
        DB::table('orders')->insert([
            ['customer_id' => 1, 'order_no' => 'ORD12345', 'transaction_id' => 'TXN12345', 'payment_method' => 'Credit Card', 'card_last_digit' => '1234', 'paid_amount' => 250.00, 'booking_date' => now(), 'status' => 'Confirmed', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

class OrderDetailSeeder extends Seeder
{
    public function run()
    {
        DB::table('order_details')->insert([
            ['order_id' => 1, 'room_id' => 1, 'order_no' => 'ORD12345', 'checkin_date' => now(), 'checkout_date' => Carbon::now()->addDays(2), 'adult' => 2, 'children' => 1, 'subtotal' => 250.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}