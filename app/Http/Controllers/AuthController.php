<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserValidate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;

class AuthController extends Controller
{

    public function register(UserValidate $request)
    {
        DB::beginTransaction();

        try {
            $role = $request['role'];

            // Only allow admins to create admin accounts
            if ($role === 'admin' && (!Auth::check() || Auth::user()->role !== 'admin')) {
                return redirect()->route('login')->with([
                    'status' => 0,
                    'message' => 'Only admins can create admin accounts.',
                    'error' => 'Unauthorized role assignment',
                ]);
            }

            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'gender' => $request['gender'],
                'phoneno' => $request['phoneno'],
                'address' => $request['address'],
                'district' => $request['district'],
                'province' => $request['province'],
                'country' => $request['country'],
                'date_of_birth' => $request['date_of_birth'],
                'role' => $request['role'],
            ]);

            // Trigger the Registered event and send email verification
            event(new Registered($user));
            Log::info('Verification email sent to: ' . $user->email);

            DB::commit();

            return redirect()->route('login')->with([
                'status' => 1,
                'message' => 'Registration successful. Please verify your email.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration failed: ' . $e->getMessage());

            return redirect()->route('login')->with([
                'status' => 0,
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function login(Request $request)
    {
        $checkUser = User::where('email',$request->email )->where('delete_flag',0)->first();
        if(!$checkUser){
            return redirect()->route('home')->with([
                'status' => 0,
                'error' => 'User not found. Please register first.',
            ]);
        }
        
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return redirect()->route('login')->with([
                    'status' => 0,
                    'message' => 'Please verify your email first.',
                ]);
            }
            
            $request->session()->regenerate();
            

            $checkRole = $request->role;
            if ($user->role === 'admin' && $checkRole === 'admin') {
                return redirect()->route('admin.dashboard')->with([
                    'status' => 1,
                    'message' => 'Welcome ' . $user->name . '!',
                ]);
            } elseif ($user->role === 'user') {
                return redirect()->route('user.dashboard')->with([
                    'status' => 1,
                    'message' => 'Welcome ' . $user->name . '!',
                ]);
            } else {
                abort(403, 'Unauthorized action.');
            }
        }

        return redirect()->route('login')->with([
            'status' => 0,
            'message' => 'Invalid credentials',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with([
            'status' => 1,
            'message' => 'Logged out successfully',
        ]);
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with([
                'status' => 0,
                'message' => 'Invalid verification link.',
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
            Log::info('Email verified for user: ' . $user->email);
        }

        return redirect()->route('login')->with([
            'status' => 1,
            'message' => 'Email verified successfully!',
        ]);
    }

    public function sendVerificationEmail(Request $request)
    {
        try {
            if ($request->user() && !$request->user()->hasVerifiedEmail()) {
                $request->user()->sendEmailVerificationNotification();
                Log::info('Verification email resent to: ' . $request->user()->email);
                return redirect()->route('login')->with([
                    'status' => 1,
                    'message' => 'Verification link sent',
                ]);
            }
            return redirect()->route('login')->with([
                'status' => 0,
                'message' => 'Already verified or not logged in',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend verification email: ' . $e->getMessage());
            return redirect()->route('login')->with([
                'status' => 0,
                'message' => 'Failed to resend verification email.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}