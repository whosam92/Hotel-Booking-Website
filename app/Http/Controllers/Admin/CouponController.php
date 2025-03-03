<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Hotel;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $hotels = Hotel::all(); // Fetch hotels for hotel-specific coupons
        return view('admin.coupons.create', compact('hotels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'discount_amount' => 'required|numeric|min:1',
            'discount_type' => 'required|in:fixed,percentage',
            'hotel_id' => 'nullable|exists:hotels,id',
            'status' => 'required|boolean',
        ]);

        Coupon::create($request->all());
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        $hotels = Hotel::all();
        return view('admin.coupons.edit', compact('coupon', 'hotels'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'discount_amount' => 'required|numeric|min:1',
            'discount_type' => 'required|in:fixed,percentage',
            'hotel_id' => 'nullable|exists:hotels,id',
            'status' => 'required|boolean',
        ]);

        $coupon->update($request->all());
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}
