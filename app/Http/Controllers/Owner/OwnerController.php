<?php

namespace App\Http\Controllers\Owner; 


use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Order;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Owner;
use Illuminate\Http\Request;


class OwnerController extends Controller
{
    public function index()
    {
        $total_completed_orders = Order::where('status','Completed')->count();
        $total_pending_orders = Order::where('status','Pending')->count();
        $total_active_customers = Customer::where('status',1)->count();
        $total_pending_customers = Customer::where('status',0)->count();
        $total_rooms = Room::count();

        $orders = Order::orderBy('id','desc')->skip(0)->take(5)->get();

        return view('owner.home', compact('total_completed_orders','total_pending_orders','total_active_customers','total_pending_customers','total_rooms','orders'));
    }
    
    public function showHotels()
{
    $hotels = Hotel::all();
    return view('owner.hotel.index', compact('hotels'));
}
    public function createHotel()
{
    return view('owner.hotel.create');
}

public function storeHotel(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'required|string',
        'rental_location' => 'required|string',
        'one_day_rental_price' => 'required|numeric',
        'pictures.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
    ]);

    $pictures = [];
    if ($request->hasFile('pictures')) {
        foreach ($request->file('pictures') as $file) {
            $pictures[] = $file->store('hotels', 'public'); 
        }
    }

    Hotel::create([
        'name' => $request->name,
        'address' => $request->address,
        'rental_location' => $request->rental_location,
        'one_day_rental_price' => $request->one_day_rental_price,
        'pictures' => json_encode($pictures), 
    ]);

    return redirect()->route('owner.hotel.index')->with('success', 'Hotel added successfully!');
}


}
