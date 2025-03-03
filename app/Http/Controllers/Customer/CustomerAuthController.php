<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Page; // Add this at the top



class CustomerAuthController extends Controller
{
   

public function login()
{
    $global_page_data = Page::first(); // Fetch page settings
    return view('front.login', compact('global_page_data'));
}




    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credential = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ];

        if (Auth::guard('customer')->attempt($credential)) {
            return redirect()->route('home'); // send cx to home page after login success
        } else {
            return redirect()->route('customer_login')->with('error', 'Information is not correct!');
        }
    }

    public function signup()
    {
        $global_page_data = Page::first(); // Fetch page settings
        return view('front.signup', compact('global_page_data'));
    }

    public function signup_submit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:customers',
            'password' => 'required',
            'retype_password' => 'required|same:password'
        ]);

        $password = Hash::make($request->password);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->password = $password;
        $customer->status = 1; // Automatically activate the user
        $customer->save();

        return redirect()->route('customer_login')->with('success', 'Your account has been created successfully!');
    }

    public function logout()
    {
        Auth::guard('customer')->logout();

            // 🔹 Clear coupon-related session data on logout
    session()->forget('discount_amount');
    session()->forget('final_total');
    session()->forget('applied_coupon');

        return redirect()->route('customer_login')->with('success', 'You have been logged out.');
    }

    // public function forget_password()
    // {
    //     return view('front.forget_password');
    // }

    // public function forget_password_submit(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email'
    //     ]);

    //     $customer_data = Customer::where('email', $request->email)->first();
    //     if (!$customer_data) {
    //         return redirect()->back()->with('error', 'Email address not found!');
    //     }

    //     return redirect()->route('customer_login')->with('success', 'Please check your email for password reset instructions.');
    // }

    // public function reset_password($token, $email)
    // {
    //     $customer_data = Customer::where('token', $token)->where('email', $email)->first();
    //     if (!$customer_data) {
    //         return redirect()->route('customer_login');
    //     }

    //     return view('front.reset_password', compact('token', 'email'));
    // }

    // public function reset_password_submit(Request $request)
    // {
    //     $request->validate([
    //         'password' => 'required',
    //         'retype_password' => 'required|same:password'
    //     ]);

    //     $customer_data = Customer::where('token', $request->token)->where('email', $request->email)->first();

    //     $customer_data->password = Hash::make($request->password);
    //     $customer_data->token = '';
    //     $customer_data->update();

    //     return redirect()->route('customer_login')->with('success', 'Password is reset successfully');
    // }
}
