<?php

namespace App\Http\Controllers\owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amenity;
use App\Models\Room;
use App\Models\RoomPhoto;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;

class OwnerRoomController extends Controller
{
    public function index()
    {
        $owner_id = Auth::id(); // Get logged-in owner ID
        $rooms = Room::whereHas('hotel', function ($query) use ($owner_id) {
            $query->where('owner_id', $owner_id);
        })->get();
        

        return view('owner.room.room_view', compact('rooms'));
    }

    public function add()
    {
        $owner_id = Auth::id();
        $hotels = Hotel::where('owner_id', $owner_id)->get();
        $all_amenities = Amenity::get();

        return view('owner.room.add', compact('all_amenities', 'hotels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'featured_photo' => 'required|image|mimes:jpg,jpeg,png,gif',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
        ]);

        // Check if the hotel belongs to the logged-in owner
        $hotel = Hotel::where('id', $request->hotel_id)
                      ->where('owner_id', Auth::id())
                      ->firstOrFail();

        // Handle amenities
        $amenities = $request->arr_amenities ? implode(',', $request->arr_amenities) : '';

        // Handle photo upload
        $ext = $request->file('featured_photo')->extension();
        $final_name = time() . '.' . $ext;
        $request->file('featured_photo')->move(public_path('uploads/'), $final_name);

        // Create room
        Room::create([
            'hotel_id' => $hotel->id,
            'featured_photo' => $final_name,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'amenities' => $amenities,
            'size' => $request->size,
            'total_beds' => $request->total_beds,
            'total_bathrooms' => $request->total_bathrooms,
            'total_balconies' => $request->total_balconies,
            'total_guests' => $request->total_guests,
            'video_id' => $request->video_id,
        ]);

        return redirect()->back()->with('success', 'Room is added successfully.');
    }

    public function edit($id)
    {
        $owner_id = Auth::id();
        $hotels = Hotel::where('owner_id', $owner_id)->get();

        $room = Room::where('id', $id)->whereHas('hotel', function ($query) use ($owner_id) {
            $query->where('owner_id', $owner_id);
        })->firstOrFail();

        $all_amenities = Amenity::get();
        $hotels = Hotel::where('owner_id', $owner_id)->get();
        $existing_amenities = $room->amenities ? explode(',', $room->amenities) : [];

        return view('owner.room.room_edit', compact('room', 'all_amenities', 'existing_amenities', 'hotels'));
    }

    public function update(Request $request, $id)
    {
        $room = Room::where('id', $id)->whereHas('hotel', function ($query) {
            $query->where('owner_id', Auth::id());
        })->firstOrFail();

        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'total_rooms' => 'required',
        ]);

        // Check if the selected hotel belongs to the logged-in owner
        Hotel::where('id', $request->hotel_id)
             ->where('owner_id', Auth::id())
             ->firstOrFail();

        $amenities = $request->arr_amenities ? implode(',', $request->arr_amenities) : '';

        if ($request->hasFile('featured_photo')) {
            $request->validate(['featured_photo' => 'image|mimes:jpg,jpeg,png,gif']);
            unlink(public_path('uploads/' . $room->featured_photo));
            $ext = $request->file('featured_photo')->extension();
            $final_name = time() . '.' . $ext;
            $request->file('featured_photo')->move(public_path('uploads/'), $final_name);
            $room->featured_photo = $final_name;
        }

        $room->update([
            'hotel_id' => $request->hotel_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'amenities' => $amenities,
            'size' => $request->size,
            'total_beds' => $request->total_beds,
            'total_bathrooms' => $request->total_bathrooms,
            'total_balconies' => $request->total_balconies,
            'total_guests' => $request->total_guests,
            'video_id' => $request->video_id,
        ]);

        return redirect()->back()->with('success', 'Room is updated successfully.');
    }

    public function delete($id)
    {
        $room = Room::where('id', $id)->whereHas('hotel', function ($query) {
            $query->where('owner_id', Auth::id());
        })->firstOrFail();

        unlink(public_path('uploads/' . $room->featured_photo));
        $room->delete();

        $room_photos = RoomPhoto::where('room_id', $id)->get();
        foreach ($room_photos as $photo) {
            unlink(public_path('uploads/' . $photo->photo));
            $photo->delete();
        }

        return redirect()->back()->with('success', 'Room is deleted successfully.');
    }
    public function gallery($roomId)
    {
        // Fetch room details with photos
        $room_data = Room::find($roomId);
    
        if (!$room_data) {
            return redirect()->back()->with('error', 'Room not found.');
        }
    
        // Fetch the room's photos
        $room_photos = RoomPhoto::where('room_id', $roomId)->get();
    
        return view('owner.room.gallery', compact('room_data', 'room_photos'));
    }
    
    public function gallery_delete($photoId)
    {
        // Find the photo record
        $photo = RoomPhoto::find($photoId);
    
        if (!$photo) {
            return redirect()->back()->with('error', 'Photo not found.');
        }
    
        // Delete the photo from storage
        if (file_exists(public_path('uploads/' . $photo->photo))) {
            unlink(public_path('uploads/' . $photo->photo));
        }
    
        // Delete the record from the database
        $photo->delete();
    
        return redirect()->back()->with('success', 'Photo deleted successfully.');
    }
    
    public function gallery_store(Request $request, $roomId)
    {
        // Validate the uploaded files
        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
    
        // Find the room
        $room = Room::find($roomId);
        if (!$room) {
            return redirect()->back()->with('error', 'Room not found.');
        }
    
        // Process multiple image uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                // Generate a unique filename
                $filePath = time() . '_' . $file->getClientOriginalName();
                
                // Move file to storage (ensure the 'uploads' folder exists in "public")
                $file->move(public_path('uploads'), $filePath);
    
                // Save filename in the database
                RoomPhoto::create([
                    'room_id' => $room->id,
                    'photo' => $filePath  // ✅ Store only the filename, not full path
                ]);
            }
        }
    
        return redirect()->back()->with('success', 'Photos uploaded successfully.');
    }
    


}
