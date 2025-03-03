<?php

namespace App\Http\Controllers\owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\BookedRoom;

class OwnerDatewiseRoomController extends Controller
{
    public function index()
    {
        return view('owner.room.index');
    }

    public function show(Request $request) 
    {
        $request->validate([
            'selected_date' => 'required'
        ]);

        $selected_date = $request->selected_date;

        return view('owner.room.datewise_rooms_detail', compact('selected_date'));
    }
}
