<?php

namespace App\Http\Controllers\owner;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerHotelController extends Controller
{
    public function index()
    {
        // Get hotels for the authenticated owner
        $hotels = Hotel::where('owner_id', Auth::id())->get();
        return view('owner.hotel.hotel_view', compact('hotels')); 
    }

    public function create()
    {
        // No need to select owner - use authenticated owner
        return view('owner.hotel.hotel_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    
        // Automatically assign to logged-in owner
        Hotel::create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'owner_id' => Auth::id(),
        ]);
    
        return redirect()->route('owner.hotel_view')->with('success', 'Hotel added successfully.');
    }

    public function edit($id)
    {
        $hotel = Hotel::findOrFail($id);
        
        // Verify ownership
        if ($hotel->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        return view('owner.hotel.hotel_edit', compact('hotel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    
        $hotel = Hotel::findOrFail($id);
        
        // Verify ownership
        if ($hotel->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        $hotel->update([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            // Owner_id remains unchanged
        ]);
    
        return redirect()->route('owner.hotel_view')->with('success', 'Hotel updated successfully.');
    }

    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        
        // Verify ownership
        if ($hotel->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $hotel->delete();

        return redirect()->route('owner.hotel_view')->with('success', 'Hotel deleted successfully.');
    }
}