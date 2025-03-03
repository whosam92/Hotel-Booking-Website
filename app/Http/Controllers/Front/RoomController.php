<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Page;
use App\Models\Review;
use App\Models\BookedRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    /**
     * Display the list of all available rooms.
     */
    public function index()
    {
        $room_all = Room::paginate(12);
        $global_page_data = Page::first(); // Fetch page settings

        return view('front.room', compact('room_all', 'global_page_data'));
    }

    /**
     * Display details for a single room, including its reviews.
     */
    public function single_room($id)
    {
        // Fetch room details along with related photos and reviews
        $single_room_data = Room::with(['rRoomPhoto', 'reviews.customer'])
            ->where('id', $id)
            ->firstOrFail();

        // Fetch page settings
        $global_page_data = Page::first();

        // Fetch paginated reviews
        $reviews = Review::where('room_id', $id)
            ->with('customer')
            ->latest()
            ->paginate(5);

        // Check if logged-in customer has booked this room
        $hasBooked = false;
        $disabled_dates = [];  // Initialize the array for disabled dates

        if (Auth::guard('customer')->check()) {
            $customer_id = Auth::guard('customer')->id();
            
            // Check if the customer has previously booked this room
            $hasBooked = DB::table('orders')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('orders.customer_id', $customer_id)
                ->where('order_details.room_id', $id)
                ->exists();

            // Fetch already booked dates for this room from completed orders
            $booked_dates = BookedRoom::where("room_id", $id)
            ->join("orders", "booked_rooms.order_no", "=", "orders.order_no") // ✅ Corrected
            ->where("orders.status", "Completed") // Only consider confirmed bookings
            ->pluck("booked_rooms.booking_date")
            ->toArray();
                
            // Fetch dates already in the cart for this customer
            $cart_checkin_dates = session('cart_checkin_date', []);
            $cart_checkout_dates = session('cart_checkout_date', []);
            $cart_booked_dates = [];

            foreach ($cart_checkin_dates as $index => $checkin) {
                $checkout = $cart_checkout_dates[$index];
                $d1 = strtotime(str_replace('/', '-', $checkin));
                $d2 = strtotime(str_replace('/', '-', $checkout));

                while ($d1 <= $d2) {
                    $cart_booked_dates[] = date('d/m/Y', $d1);
                    $d1 = strtotime('+1 day', $d1);
                }
            }

            // Merge both booked lists into $disabled_dates
            $disabled_dates = array_unique(array_merge($booked_dates, $cart_booked_dates));
        }

        return view('front.room_detail', compact('single_room_data', 'global_page_data', 'reviews', 'hasBooked', 'disabled_dates'));
    }
}
