<?php

namespace App\Http\Controllers\Front; // Updated namespace

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // Ensure this is imported
use App\Models\ReviewReply;
use Illuminate\Support\Facades\Auth;

class ReviewReplyController extends Controller
{
    public function store(Request $request, $reviewId)
{
    $request->validate([
        'reply' => 'required|string',
    ]);

    $adminId = Auth::guard('admin')->id(); // Ensure admin is authenticated using the correct guard

    if (!$adminId) {
        return redirect()->back()->with('error', 'Unauthorized: You must be logged in as an admin.');
    }

    ReviewReply::create([
        'review_id' => $reviewId,
        'admin_id' => $adminId, // Store the correct admin ID
        'reply' => $request->reply,
    ]);

    return redirect()->back()->with('success', 'Reply added successfully!');
}

}
