<?php

namespace App\Http\Controllers\owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;

class OwnerOrderController extends Controller
{
    public function index()
    {
        $orders = Order::get();
        return view('owner.orders', compact('orders'));
    }

    public function invoice($id)
    {
        $order = Order::where('id',$id)->first();
        $order_detail = OrderDetail::where('order_id',$id)->get();
        $customer_data = Customer::where('id',$order->customer_id)->first();
        return view('owner.invoice', compact('order','order_detail','customer_data'));
    }

    public function delete($id)
    {
        $obj = Order::where('id',$id)->delete();
        $obj = OrderDetail::where('order_id',$id)->delete();

        return redirect()->back()->with('success', 'Order is deleted successfully');
    }
}
