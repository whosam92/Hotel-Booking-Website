<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\BookedRoom;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\Websitemail;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
Use Stripe;
use App\Models\Page; // Add this at the top
use App\Models\Coupon; // Add this at the top


class BookingController extends Controller
{
    
    public function cart_submit(Request $request)
    {
        // Validate request
        $request->validate([
            'room_id' => 'required',
            'checkin_checkout' => 'required',
            'adult' => 'required'
        ]);
    
        // Extract check-in and check-out dates
        $dates = explode(' - ', $request->checkin_checkout);
        $checkin_date = $dates[0]; // Format: "d/m/Y"
        $checkout_date = $dates[1]; // Format: "d/m/Y"
    
        // Convert to "Y-m-d" format for better date comparison
        $d1 = explode('/', $checkin_date);
        $d2 = explode('/', $checkout_date);
        $d1_new = $d1[2] . '-' . $d1[1] . '-' . $d1[0]; // Check-in converted
        $d2_new = $d2[2] . '-' . $d2[1] . '-' . $d2[0]; // Check-out converted
        $t1 = strtotime($d1_new);
        $t2 = strtotime($d2_new);
    
        // 🔹 Prevent duplicate room bookings for the same dates
        $cart_rooms = session('cart_room_id', []);
        $cart_checkin_dates = session('cart_checkin_date', []);
        $cart_checkout_dates = session('cart_checkout_date', []);
    
        foreach ($cart_rooms as $index => $cart_room_id) {
            if ($cart_room_id == $request->room_id) {
                $existing_checkin = strtotime(str_replace('/', '-', $cart_checkin_dates[$index]));
                $existing_checkout = strtotime(str_replace('/', '-', $cart_checkout_dates[$index]));
    
                // Check if the new date range overlaps with an existing one in the cart
                if (($t1 >= $existing_checkin && $t1 < $existing_checkout) || 
                    ($t2 > $existing_checkin && $t2 <= $existing_checkout) || 
                    ($t1 <= $existing_checkin && $t2 >= $existing_checkout)) {
                    return redirect()->back()->with('error', 'You have already added this room for the selected dates in your cart.');
                }
            }
        }
    
        // 🔹 Check if the room is fully booked
        $cnt = 1;
        while (true) {
            if ($t1 >= $t2) {
                break;
            }
            $single_date = date('d/m/Y', $t1);
            $total_already_booked_rooms = BookedRoom::where('booking_date', $single_date)
                ->where('room_id', $request->room_id)
                ->count();
    
            $room_data = Room::where('id', $request->room_id)->first();
            $total_allowed_rooms = $room_data->total_rooms;
    
            if ($total_already_booked_rooms == $total_allowed_rooms) {
                $cnt = 0;
                break;
            }
            $t1 = strtotime('+1 day', $t1);
        }
    
        if ($cnt == 0) {
            return redirect()->back()->with('error', 'This room is already fully booked for the selected dates.');
        }
    
        // 🔹 Add room details to session
        session()->push('cart_room_id', $request->room_id);
        session()->push('cart_checkin_date', $checkin_date);
        session()->push('cart_checkout_date', $checkout_date);
        session()->push('cart_adult', $request->adult);
        session()->push('cart_children', $request->children ?? 0);
    
        return redirect()->back()->with('success', 'Room added to cart successfully.');
    }
    

    public function cart_view()
{
    $global_page_data = Page::first(); // Fetch page settings

    return view('front.cart', compact('global_page_data'));
}
    public function cart_delete($id)
    {
        $arr_cart_room_id = array();
        $i=0;
        foreach(session()->get('cart_room_id') as $value) {
            $arr_cart_room_id[$i] = $value;
            $i++;
        }

        $arr_cart_checkin_date = array();
        $i=0;
        foreach(session()->get('cart_checkin_date') as $value) {
            $arr_cart_checkin_date[$i] = $value;
            $i++;
        }

        $arr_cart_checkout_date = array();
        $i=0;
        foreach(session()->get('cart_checkout_date') as $value) {
            $arr_cart_checkout_date[$i] = $value;
            $i++;
        }

        $arr_cart_adult = array();
        $i=0;
        foreach(session()->get('cart_adult') as $value) {
            $arr_cart_adult[$i] = $value;
            $i++;
        }

        $arr_cart_children = array();
        $i=0;
        foreach(session()->get('cart_children') as $value) {
            $arr_cart_children[$i] = $value;
            $i++;
        }

        session()->forget('cart_room_id');
        session()->forget('cart_checkin_date');
        session()->forget('cart_checkout_date');
        session()->forget('cart_adult');
        session()->forget('cart_children');

        for($i=0;$i<count($arr_cart_room_id);$i++)
        {
            if($arr_cart_room_id[$i] == $id) 
            {
                continue;    
            }
            else
            {
                session()->push('cart_room_id',$arr_cart_room_id[$i]);
                session()->push('cart_checkin_date',$arr_cart_checkin_date[$i]);
                session()->push('cart_checkout_date',$arr_cart_checkout_date[$i]);
                session()->push('cart_adult',$arr_cart_adult[$i]);
                session()->push('cart_children',$arr_cart_children[$i]);
            }
        }

        return redirect()->back()->with('success', 'Cart item is deleted.');

    }


// checkout function
public function checkout()
{
    if (!Auth::guard('customer')->check()) {
        return redirect()->back()->with('error', 'You must log in to proceed with checkout.');
    }

    if (!session()->has('cart_room_id')) {
        return redirect()->back()->with('error', 'There are no items in the cart.');
    }

    // Initialize total price
    $total_price = 0;
    $cart_rooms = session()->get('cart_room_id', []);
    $cart_checkin_dates = session()->get('cart_checkin_date', []);
    $cart_checkout_dates = session()->get('cart_checkout_date', []);

    foreach ($cart_rooms as $i => $room_id) {
        $room_data = Room::find($room_id);
        if ($room_data) {
            $checkin = \Carbon\Carbon::createFromFormat('d/m/Y', $cart_checkin_dates[$i]);
            $checkout = \Carbon\Carbon::createFromFormat('d/m/Y', $cart_checkout_dates[$i]);
            $diff = $checkin->diffInDays($checkout);
            $total_price += ($room_data->price * $diff);
        }
    }

    // Check if discount exists in session
    $discount_amount = session()->get('discount_amount', 0);
    $final_total = max(0, $total_price - $discount_amount);

    // Update session values to ensure correct data
    session()->put('total_price', $total_price);
    session()->put('final_total', $final_total);

    $global_page_data = Page::first();
    return view('front.checkout', compact('global_page_data', 'total_price', 'discount_amount', 'final_total'));
}

    /**
     * Handle payment request (with coupon support).
     */public function payment()
{
    if (!Auth::guard('customer')->check()) {
        return redirect()->back()->with('error', 'You must log in to proceed with checkout.');
    }

    if (!session()->has('cart_room_id')) {
        return redirect()->back()->with('error', 'There are no items in the cart.');
    }

    // 🔹 Ensure the final total (after discount) is retrieved correctly
    $final_total = session()->get('final_total', session()->get('total_price', 0));
    $discount = session()->get('discount_amount', 0);

    // 🔹 Store it again in the session to make sure it persists
    session()->put('final_total', $final_total);

    $global_page_data = Page::first();
    return view('front.payment', compact('global_page_data', 'discount', 'final_total'));
}



// checkout submit function

public function checkout_submit(Request $request)
{
    $total_price = session()->get('total_price');

    // Check for applied coupon
    if ($request->has('coupon_code')) {
        $coupon = Coupon::where('code', $request->coupon_code)->where('status', 1)->first();

        if (!$coupon) {
            return redirect()->back()->with('error', 'Invalid or expired coupon.');
        }

        // Calculate discount
        if ($coupon->discount_type === 'fixed') {
            $discount = $coupon->discount_amount;
        } else { // Percentage
            $discount = ($total_price * $coupon->discount_amount) / 100;
        }

        $total_price -= $discount;
        session()->put('discount_amount', $discount);
    }

    // Store final amount in session
    session()->put('total_price', $total_price);

    return redirect()->route('payment');
}




//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    public function paypal($final_price)
    {
        $client = 'ARw2VtkTvo3aT7DILgPWeSUPjMK_AS5RlMKkUmB78O8rFCJcfX6jFSmTDpgdV3bOFLG2WE-s11AcCGTD';
        $secret = 'EPi7BbZ0b5GP9jmy095MyNkfYjJc3PF42fC58emf-FXRZF7kEUmHKpV0rfGl6EEWXUx0TSvo0FmXkzuy';

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $client, // ClientID
                $secret // ClientSecret
            )
        );

        $paymentId = request('paymentId');
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId(request('PayerID'));

        $transaction = new Transaction();
        $amount = new Amount();
        $details = new Details();

        $details->setShipping(0)
            ->setTax(0)
            ->setSubtotal($final_price);

        $amount->setCurrency('USD');
        $amount->setTotal($final_price);
        $amount->setDetails($details);
        $transaction->setAmount($amount);
        $execution->addTransaction($transaction);
        $result = $payment->execute($execution, $apiContext);

        if($result->state == 'approved')
        {
            $paid_amount = $result->transactions[0]->amount->total;
            
            $order_no = time();

            $statement = DB::select("SHOW TABLE STATUS LIKE 'orders'");
            $ai_id = $statement[0]->Auto_increment;

            $obj = new Order();
$obj->customer_id = Auth::guard('customer')->user()->id;
$obj->order_no = $order_no;
$obj->transaction_id = $result->id;
$obj->payment_method = 'PayPal';
$obj->subtotal = session()->get('total_price', 0);
$obj->discount_amount = session()->get('discount_amount', 0);
$obj->paid_amount = session()->get('final_total', 0); // This should store the actual paid amount
$obj->booking_date = date('d/m/Y');
$obj->status = 'Completed';
$obj->save();

            
            $arr_cart_room_id = array();
            $i=0;
            foreach(session()->get('cart_room_id') as $value) {
                $arr_cart_room_id[$i] = $value;
                $i++;
            }

            $arr_cart_checkin_date = array();
            $i=0;
            foreach(session()->get('cart_checkin_date') as $value) {
                $arr_cart_checkin_date[$i] = $value;
                $i++;
            }

            $arr_cart_checkout_date = array();
            $i=0;
            foreach(session()->get('cart_checkout_date') as $value) {
                $arr_cart_checkout_date[$i] = $value;
                $i++;
            }

            $arr_cart_adult = array();
            $i=0;
            foreach(session()->get('cart_adult') as $value) {
                $arr_cart_adult[$i] = $value;
                $i++;
            }

            $arr_cart_children = array();
            $i=0;
            foreach(session()->get('cart_children') as $value) {
                $arr_cart_children[$i] = $value;
                $i++;
            }

            for($i=0;$i<count($arr_cart_room_id);$i++)
            {
                $r_info = Room::where('id',$arr_cart_room_id[$i])->first();
                $d1 = explode('/',$arr_cart_checkin_date[$i]);
                $d2 = explode('/',$arr_cart_checkout_date[$i]);
                $d1_new = $d1[2].'-'.$d1[1].'-'.$d1[0];
                $d2_new = $d2[2].'-'.$d2[1].'-'.$d2[0];
                $t1 = strtotime($d1_new);
                $t2 = strtotime($d2_new);
                $diff = ($t2-$t1)/60/60/24;
                $sub = $r_info->price*$diff;

                $obj = new OrderDetail();
                $obj->order_id = $ai_id;
                $obj->room_id = $arr_cart_room_id[$i];
                $obj->order_no = $order_no;
                $obj->checkin_date = $arr_cart_checkin_date[$i];
                $obj->checkout_date = $arr_cart_checkout_date[$i];
                $obj->adult = $arr_cart_adult[$i];
                $obj->children = isset($arr_cart_children[$i]) ? $arr_cart_children[$i] : 0;
                $obj->subtotal = $sub;
                $obj->save();

                while(1) {
                    if($t1>=$t2) {
                        break;
                    }
    
                    $obj = new BookedRoom();
                    $obj->booking_date = date('d/m/Y',$t1);
                    $obj->order_no = $order_no;
                    $obj->room_id = $arr_cart_room_id[$i];
                    $obj->save();
    
                    $t1 = strtotime('+1 day',$t1);
                }

            }

            $subject = 'New Order';
            $message = 'You have made an order for hotel booking. The booking information is given below: <br>';
            $message .= '<br>Order No: '.$order_no;
            $message .= '<br>Transaction Id: '.$result->id;
            $message .= '<br>Payment Method: PayPal';
            $message .= '<br>Paid Amount: '.$paid_amount;
            $message .= '<br>Booking Date: '.date('d/m/Y').'<br>';

            for($i=0;$i<count($arr_cart_room_id);$i++) {

                $r_info = Room::where('id',$arr_cart_room_id[$i])->first();

                $message .= '<br>Room Name: '.$r_info->name;
                $message .= '<br>Price Per Night: $'.$r_info->price;
                $message .= '<br>Checkin Date: '.$arr_cart_checkin_date[$i];
                $message .= '<br>Checkout Date: '.$arr_cart_checkout_date[$i];
                $message .= '<br>Adult: '.$arr_cart_adult[$i];
                $message .= '<br>Children: '.$arr_cart_children[$i].'<br>';
            }            

            $customer_email = Auth::guard('customer')->user()->email;

            // \Mail::to($customer_email)->send(new Websitemail($subject,$message));

            session()->forget('cart_room_id');
            session()->forget('cart_checkin_date');
            session()->forget('cart_checkout_date');
            session()->forget('cart_adult');
            session()->forget('cart_children');
            session()->forget('billing_name');
            session()->forget('billing_email');
            session()->forget('billing_phone');
            session()->forget('billing_country');
            session()->forget('billing_address');
            session()->forget('billing_state');
            session()->forget('billing_city');
            session()->forget('billing_zip');

            return redirect()->route('home')->with('success', 'Payment is successful');
        }
        else
        {
            return redirect()->route('home')->with('error', 'Payment is failed');
        }


    }

    public function stripe(Request $request,$final_price)
    {
        $stripe_secret_key = 'sk_test_51LT28GF67T3XLjgL8ICWowviN9gL7cXzOr1hPOEVX94aizsO58jdO1CtIBpo583748yVPzAV46pivFolrjqZddSx00PSAfpyff';
        $cents = $final_price*100;
        Stripe\Stripe::setApiKey($stripe_secret_key);
        $response = Stripe\Charge::create ([
            "amount" => $cents,
            "currency" => "usd",
            "source" => $request->stripeToken,
            "description" => env('APP_NAME')
        ]);

        $responseJson = $response->jsonSerialize();
        $transaction_id = $responseJson['balance_transaction'];
        $last_4 = $responseJson['payment_method_details']['card']['last4'];

        $order_no = time();

        $statement = DB::select("SHOW TABLE STATUS LIKE 'orders'");
        $ai_id = $statement[0]->Auto_increment;

        $obj = new Order();
        $obj->customer_id = Auth::guard('customer')->user()->id;
        $obj->order_no = $order_no;
        $obj->transaction_id = $transaction_id;
        $obj->payment_method = 'Stripe';
        $obj->card_last_digit = $last_4;
        $obj->subtotal = session()->get('total_price', 0);
        $obj->discount_amount = session()->get('discount_amount', 0);
        $obj->paid_amount = session()->get('final_total', 0); // This should store the actual paid amount
        $obj->booking_date = date('d/m/Y');
        $obj->status = 'Completed';
        $obj->save();
        
        
        $arr_cart_room_id = array();
        $i=0;
        foreach(session()->get('cart_room_id') as $value) {
            $arr_cart_room_id[$i] = $value;
            $i++;
        }

        $arr_cart_checkin_date = array();
        $i=0;
        foreach(session()->get('cart_checkin_date') as $value) {
            $arr_cart_checkin_date[$i] = $value;
            $i++;
        }

        $arr_cart_checkout_date = array();
        $i=0;
        foreach(session()->get('cart_checkout_date') as $value) {
            $arr_cart_checkout_date[$i] = $value;
            $i++;
        }

        $arr_cart_adult = array();
        $i=0;
        foreach(session()->get('cart_adult') as $value) {
            $arr_cart_adult[$i] = $value;
            $i++;
        }

        $arr_cart_children = array();
        $i=0;
        foreach(session()->get('cart_children') as $value) {
            $arr_cart_children[$i] = $value;
            $i++;
        }

        for($i=0;$i<count($arr_cart_room_id);$i++)
        {
            $r_info = Room::where('id',$arr_cart_room_id[$i])->first();
            $d1 = explode('/',$arr_cart_checkin_date[$i]);
            $d2 = explode('/',$arr_cart_checkout_date[$i]);
            $d1_new = $d1[2].'-'.$d1[1].'-'.$d1[0];
            $d2_new = $d2[2].'-'.$d2[1].'-'.$d2[0];
            $t1 = strtotime($d1_new);
            $t2 = strtotime($d2_new);
            $diff = ($t2-$t1)/60/60/24;
            $sub = $r_info->price*$diff;

            $obj = new OrderDetail();
            $obj->order_id = $ai_id;
            $obj->room_id = $arr_cart_room_id[$i];
            $obj->order_no = $order_no;
            $obj->checkin_date = $arr_cart_checkin_date[$i];
            $obj->checkout_date = $arr_cart_checkout_date[$i];
            $obj->adult = $arr_cart_adult[$i];
            $obj->children = isset($arr_cart_children[$i]) ? $arr_cart_children[$i] : 0;
            $obj->subtotal = $sub;
            $obj->save();

            while(1) {
                if($t1>=$t2) {
                    break;
                }

                $obj = new BookedRoom();
                $obj->booking_date = date('d/m/Y',$t1);
                $obj->order_no = $order_no;
                $obj->room_id = $arr_cart_room_id[$i];
                $obj->save();

                $t1 = strtotime('+1 day',$t1);
            }

        }

        $subject = 'New Order';
        $message = 'You have made an order for hotel booking. The booking information is given below: <br>';
        $message .= '<br>Order No: '.$order_no;
        $message .= '<br>Transaction Id: '.$transaction_id;
        $message .= '<br>Payment Method: Stripe';
        $message .= '<br>Paid Amount: '.$final_price;
        $message .= '<br>Booking Date: '.date('d/m/Y').'<br>';

        for($i=0;$i<count($arr_cart_room_id);$i++) {

            $r_info = Room::where('id',$arr_cart_room_id[$i])->first();

            $message .= '<br>Room Name: '.$r_info->name;
            $message .= '<br>Price Per Night: $'.$r_info->price;
            $message .= '<br>Checkin Date: '.$arr_cart_checkin_date[$i];
            $message .= '<br>Checkout Date: '.$arr_cart_checkout_date[$i];
            $message .= '<br>Adult: '.$arr_cart_adult[$i];
            $message .= '<br>Children: '.$arr_cart_children[$i].'<br>';
        }            

        $customer_email = Auth::guard('customer')->user()->email;

        // \Mail::to($customer_email)->send(new Websitemail($subject,$message));

        session()->forget('cart_room_id');
        session()->forget('cart_checkin_date');
        session()->forget('cart_checkout_date');
        session()->forget('cart_adult');
        session()->forget('cart_children');
        session()->forget('billing_name');
        session()->forget('billing_email');
        session()->forget('billing_phone');
        session()->forget('billing_country');
        session()->forget('billing_address');
        session()->forget('billing_state');
        session()->forget('billing_city');
        session()->forget('billing_zip');

        return redirect()->route('home')->with('success', 'Payment is successful');


    }

    //coupon apply function
    public function applyCoupon(Request $request)
{
    $request->validate([
        'coupon_code' => 'required|string|max:20',
    ]);

    if (!session()->has('cart_room_id')) {
        return response()->json(['error' => 'There are no items in the cart.'], 400);
    }

    // Check if the coupon is valid
    $coupon = Coupon::where('code', $request->coupon_code)->where('status', 1)->first();

    if (!$coupon) {
        return response()->json(['error' => 'Invalid or expired coupon.'], 400);
    }

    // Get total price from session
    $total_price = session()->get('total_price', 0);
    $discount = 0;

    // Apply discount logic
    if ($coupon->discount_type === 'fixed') {
        $discount = $coupon->discount_amount;
    } else { // Percentage discount
        $discount = ($total_price * $coupon->discount_amount) / 100;
    }

    // Ensure the final total is never negative
    $final_price = max(0, $total_price - $discount);

    // 🔹 Clear any existing coupon before applying the new one
    session()->forget('discount_amount');
    session()->forget('final_total');
    session()->forget('applied_coupon');

    // 🔹 Store only the latest discount
    session()->put('discount_amount', $discount);
    session()->put('final_total', $final_price);
    session()->put('applied_coupon', $request->coupon_code);

    return response()->json([
        'success' => 'Coupon applied successfully!',
        'discount' => $discount,
        'final_total' => $final_price
    ]);
}

    
}