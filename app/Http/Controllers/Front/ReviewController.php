<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request, $room_id)
    {
        // Ensure the user is logged in as a customer
        if (!Auth::guard('customer')->check()) {
            return redirect()->back()->with('error', 'You must be logged in to leave a review.');
        }
    
        $customer_id = Auth::guard('customer')->id();
    
        // Check if the customer has booked this room
        $hasBooked = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.customer_id', $customer_id)
            ->where('order_details.room_id', $room_id)
            ->exists();
    
        if (!$hasBooked) {
            return redirect()->back()->with('error', 'You can only review rooms that you have booked.');
        }
    
        // ✅ Validate review input
        $validatedData = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:500',  // Ensure the field is 'review'
        ]);
    
        // ✅ Create and save the review
        Review::create([
            'room_id' => $room_id,
            'customer_id' => $customer_id,
            'rating' => $validatedData['rating'],
            'review' => $validatedData['review'],
        ]);
    
        return redirect()->back()->with('success', 'Thank you for your review!');
    }

//     public function edit($id)
// {
//     $review = Review::where('id', $id)->where('customer_id', Auth::guard('customer')->id())->firstOrFail();
//     return view('reviews.edit', compact('review'));
// }

// public function update(Request $request, $id)
// {
//     $request->validate([
//         'rating' => 'required|integer|min:1|max:5',
//         'review' => 'required|string|max:500',
//     ]);

//     $review = Review::where('id', $id)->where('customer_id', Auth::guard('customer')->id())->firstOrFail();
//     $review->update([
//         'rating' => $request->rating,
//         'review' => $request->review,
//     ]);

//     return redirect()->back()->with('success', 'Review updated successfully!');
// }

public function destroy($id)
{
    $review = Review::where('id', $id)->where('customer_id', Auth::guard('customer')->id())->firstOrFail();
    $review->delete();

    return redirect()->back()->with('success', 'Review deleted successfully!');
}

public function reply(Request $request, $id)
{
    // ❌ Reject if the user is not an admin
    if (!Auth::guard('admin')->check()) {
        return redirect()->route('admin_login')->with('error', 'Only admins can reply to reviews.');
    }

    // Validate the request
    $request->validate([
        'reply' => 'required|string|max:1000',
    ]);

    // Find the review by ID
    $review = Review::findOrFail($id);

    // Store the reply
    $review->reply = $request->reply;
    $review->save();

    return back()->with('success', 'Reply added successfully.');
}





}    