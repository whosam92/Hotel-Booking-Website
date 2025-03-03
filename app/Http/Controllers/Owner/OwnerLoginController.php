<?php

namespace App\Http\Controllers\owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Mail\Websitemail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class OwnerLoginController extends Controller
{
    public function index()
    {
        return view('owner.login');
    }

    public function forget_password()
    {
        return view('owner.forget_password');
    }

    public function forget_password_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $admin_data = Owner::where('email',$request->email)->first();
        if(!$admin_data) {
            return redirect()->back()->with('error','Email address not found!');
        }

        $token = hash('sha256',time());

        $admin_data->token = $token;
        $admin_data->update();

        $reset_link = url('owner/reset-password/'.$token.'/'.$request->email);
        $subject = 'Reset Password';
        $message = 'Please click on the following link: <br>';
        $message .= '<a href="'.$reset_link.'">Click here</a>';

        // \Mail::to($request->email)->send(new Websitemail($subject,$message));

        return redirect()->route('owner_login')->with('success','Please check your email and follow the steps there');

    }

    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credential = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::guard('owner')->attempt($credential)) {
            return redirect()->route('owner_home');
        } else {
            return redirect()->route('owner_login')->with('error', 'Information is not correct!');
        }
    }

    public function logout()
    {
        Auth::guard('owner')->logout();
        return redirect()->route('owner_login');
    }

    public function reset_password($token,$email)
    {
        $admin_data = Owner::where('token',$token)->where('email',$email)->first();
        if(!$admin_data) {
            return redirect()->route('owner_login');
        }

        return view('owner.reset_password', compact('token','email'));

    }

    public function reset_password_submit(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'retype_password' => 'required|same:password'
        ]);

        $owner_data = Owner::where('token',$request->token)->where('email',$request->email)->first();

        $owner_data->password = Hash::make($request->password);
        $owner_data->token = '';
        $owner_data->update();

        return redirect()->route('owner_login')->with('success', 'Password is reset successfully');

    }
}
