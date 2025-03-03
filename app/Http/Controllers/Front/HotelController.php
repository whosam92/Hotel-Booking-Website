<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Room;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::with(['rooms' => function ($q) use ($request) {
            // Apply sorting inside the rooms relation
            if ($request->has('price_filter')) {
                if ($request->price_filter == 'low') {
                    $q->orderBy('price', 'asc');
                } elseif ($request->price_filter == 'high') {
                    $q->orderBy('price', 'desc');
                }
            }
        }]);

        // Search by hotel name or location
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('location', 'LIKE', '%' . $request->search . '%');
            });
        }

        $hotels = $query->get();

        return view('front.hotels', compact('hotels'));
    }
}
