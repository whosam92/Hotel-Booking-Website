@php
// Initialize total price
$total_price = 0;

// Retrieve cart data from session
$cart_rooms = session()->get('cart_room_id', []);
$cart_checkin_dates = session()->get('cart_checkin_date', []);
$cart_checkout_dates = session()->get('cart_checkout_date', []);
$cart_adult = session()->get('cart_adult', []);
$cart_children = session()->get('cart_children', []);

foreach ($cart_rooms as $i => $room_id) {
    $room_data = DB::table('rooms')->where('id', $room_id)->first();
    if ($room_data) {
        $d1_new = \Carbon\Carbon::createFromFormat('d/m/Y', $cart_checkin_dates[$i]);
        $d2_new = \Carbon\Carbon::createFromFormat('d/m/Y', $cart_checkout_dates[$i]);
        $diff = $d1_new->diffInDays($d2_new);
        $room_price = $room_data->price * $diff;
        $total_price += $room_price;
    }
}

// Retrieve discount from session
$discount_amount = session()->get('discount_amount', 0);
$final_total = session()->get('final_total', $total_price);
@endphp





@extends('front.layout.app')

@section('main_content')
<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $global_page_data->checkout_heading }}</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-6 checkout-left">

                <form action="{{ route('payment') }}" method="post" class="frm_checkout">
                    @csrf
                    <div class="billing-info">
                        <h4 class="mb_30">Billing Information</h4>
                        @php
                        if(session()->has('billing_name')) {
                            $billing_name = session()->get('billing_name');
                        } else {
                            $billing_name = Auth::guard('customer')->user()->name;
                        }

                        if(session()->has('billing_email')) {
                            $billing_email = session()->get('billing_email');
                        } else {
                            $billing_email = Auth::guard('customer')->user()->email;
                        }

                        if(session()->has('billing_phone')) {
                            $billing_phone = session()->get('billing_phone');
                        } else {
                            $billing_phone = Auth::guard('customer')->user()->phone;
                        }

                        if(session()->has('billing_country')) {
                            $billing_country = session()->get('billing_country');
                        } else {
                            $billing_country = Auth::guard('customer')->user()->country;
                        }

                        if(session()->has('billing_address')) {
                            $billing_address = session()->get('billing_address');
                        } else {
                            $billing_address = Auth::guard('customer')->user()->address;
                        }

                        if(session()->has('billing_state')) {
                            $billing_state = session()->get('billing_state');
                        } else {
                            $billing_state = Auth::guard('customer')->user()->state;
                        }

                        if(session()->has('billing_city')) {
                            $billing_city = session()->get('billing_city');
                        } else {
                            $billing_city = Auth::guard('customer')->user()->city;
                        }

                        if(session()->has('billing_zip')) {
                            $billing_zip = session()->get('billing_zip');
                        } else {
                            $billing_zip = Auth::guard('customer')->user()->zip;
                        }
                        @endphp
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="">Name: *</label>
                                <input type="text" class="form-control mb_15" name="billing_name" value="{{ $billing_name }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Email Address: *</label>
                                <input type="text" class="form-control mb_15" name="billing_email" value="{{ $billing_email }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Phone Number: *</label>
                                <input type="text" class="form-control mb_15" name="billing_phone" value="{{ $billing_phone }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Country: *</label>
                                <input type="text" class="form-control mb_15" name="billing_country" value="{{ $billing_country }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Address: *</label>
                                <input type="text" class="form-control mb_15" name="billing_address" value="{{ $billing_address }}">
                            </div>
                            {{-- <div class="col-lg-6">
                                <label for="">State: *</label>
                                <input type="text" class="form-control mb_15" name="billing_state" value="{{ $billing_state }}">
                            </div> --}}
                            <div class="col-lg-6">
                                <label for="">City: *</label>
                                <input type="text" class="form-control mb_15" name="billing_city" value="{{ $billing_city }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Zip Code: *</label>
                                <input type="text" class="form-control mb_15" name="billing_zip" value="{{ $billing_zip }}">
                            </div>
                        </div>
                    </div>
{{-- hidden --}}
<input type="hidden" name="final_total" id="final_total_input" value="{{ $final_total }}">
<input type="hidden" name="coupon_code" id="applied_coupon_code" value="{{ session()->get('applied_coupon', '') }}">
{{-- hidden --}}
                    <button type="submit" class="btn btn-primary bg-website mb_30">Continue to payment</button>
                </form>
            </div>
            <div class="col-lg-4 col-md-6 checkout-right">
                <div class="inner">
                    <h4 class="mb_10">Cart Details</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>

                                @php
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

                                $total_price = 0;
                                for($i=0;$i<count($arr_cart_room_id);$i++)
                                {
                                    $room_data = DB::table('rooms')->where('id',$arr_cart_room_id[$i])->first();
                                    @endphp

                                    <tr>
                                        <td>
                                            {{ $room_data->name }}
                                            <br>
                                            ({{ $arr_cart_checkin_date[$i] }} - {{ $arr_cart_checkout_date[$i] }})
                                            <br>
                                            Adult: {{ $arr_cart_adult[$i] }}, Children: {{ $arr_cart_children[$i] }}
                                        </td>
                                        <td class="p_price">
                                            @php
                                                $d1 = explode('/',$arr_cart_checkin_date[$i]);
                                                $d2 = explode('/',$arr_cart_checkout_date[$i]);
                                                $d1_new = $d1[2].'-'.$d1[1].'-'.$d1[0];
                                                $d2_new = $d2[2].'-'.$d2[1].'-'.$d2[0];
                                                $t1 = strtotime($d1_new);
                                                $t2 = strtotime($d2_new);
                                                $diff = ($t2-$t1)/60/60/24;
                                                echo '$'.$room_data->price*$diff;
                                            @endphp
                                        </td>
                                    </tr>
                                    @php
                                    $total_price = $total_price+($room_data->price*$diff);
                                }
                                @endphp  
                                <div class="form-group">
                                    <label for="coupon_code">Have a Coupon?</label>
                                    <input type="text" name="coupon_code" id="coupon_code" class="form-control" placeholder="Enter Coupon Code">
                                    <button type="button" class="btn btn-success mt-2" id="apply_coupon">Apply Coupon</button>
                                </div>
                                                              
                                <tr>
                                    <td><b>Subtotal:</b></td>
                                    <td class="p_price"><b>${{ number_format($total_price, 2) }}</b></td>
                                </tr>
                                <tr>
                                    <td><b>Discount:</b></td>
                                    <td class="p_price" id="discount_amount">- ${{ number_format(session()->get('discount_amount', 0), 2) }}</td>
                                </tr>
                                <tr>
                                    <td><b>Total:</b></td>
                                    <td class="p_price" id="final_total"><b>${{ number_format(session()->get('final_total', $total_price), 2) }}</b></td>
                                </tr>
                                
                                
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- copoun script js  --}}

<script>
    document.getElementById('apply_coupon').addEventListener('click', function () {
        let couponCode = document.getElementById('coupon_code').value;

        if (couponCode.trim() === "") {
            alert("Please enter a coupon code.");
            return;
        }

        fetch("{{ route('apply.coupon') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ coupon_code: couponCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let discountAmount = parseFloat(data.discount);
                let finalTotal = parseFloat(data.final_total);

                // Ensure correct calculations
                document.getElementById('discount_amount').innerHTML = "- $" + discountAmount.toFixed(2);
                document.getElementById('final_total').innerHTML = "$" + finalTotal.toFixed(2);

                // Update hidden input values for form submission
                document.getElementById('final_total_input').value = finalTotal;
                document.getElementById('applied_coupon_code').value = couponCode;

                alert(data.success);
            } else {
                alert(data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>





@endsection


