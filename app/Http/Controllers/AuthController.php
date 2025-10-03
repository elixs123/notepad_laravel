<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notes;
use App\Services\NoteService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
       return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid email or password.');
        }
        Auth::login($user);

        return redirect()->route('home'); 
    }


    public function register()
    {
       return view('auth.register');
    }

    public function registerPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:new_users',
            'password' => 'required|string|min:6|confirmed', 
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'is_admin' => false,
            'email' => $request->email,
        ]);

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }
}
