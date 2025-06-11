<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserValidate;
use App\Models\Country;
use App\Models\District;
use App\Models\Province;
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

    public function register(Request $request)
    {
        DB::beginTransaction();

        try {
            $role = $request['role'];

            // Check if email is already in use
            if (User::where('email', $request['email'])->exists()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This email is already in use.',
                    ], 409);
                }
                return redirect()->route('login')->with([
                    'status' => 0,
                    'message' => 'This email is already in use.',
                ]);
            }

            // Only allow admins to create admin accounts
            if ($role === 'admin' && (!Auth::check() || Auth::user()->role !== 'admin')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only admins can create admin accounts.',
                    ], 403);
                }
                return redirect()->route('login')->with([
                    'status' => 0,
                    'message' => 'Only admins can create admin accounts.',
                    'error' => 'Unauthorized role assignment',
                ]);
            }
            $country = Country::firstOrCreate(['name' => $request->input(strtolower('country_name'))]);

            $province = Province::firstOrCreate(
                ['name' => $request->input(strtolower('province_name')), 'country_id' => $country->id]
            );

            $district = District::firstOrCreate(
                ['name' => $request->input(strtolower('district_name')), 'province_id' => $province->id],
                ['country_id' => $country->id]
            );


            if (!$district || !$province || !$country) {
                return response()->json(['error' => 'Invalid location data provided.'], 422);
            }

            $user = new User();
            $user->name = $request->input(strtolower('name'));
            $user->email = $request->input(strtolower('email'));
            $user->password = Hash::make($request['password']);
            $user->gender = $request->input(strtolower('gender'));
            $user->phoneno = $request['phoneno'];
            $user->address = $request->input(strtolower('address'));
            $user->district_id = $district->id;
            $user->province_id = $province->id;
            $user->country_id = $country->id;
            $user->date_of_birth = $request['date_of_birth'];
            $user->role = $request['role'];
            $user->created_by = Auth::check() ? Auth::id() : null; // Set created_by if logged in
            $user->updated_by = Auth::check() ? Auth::id() : null; // Set updated_by if logged in
            $user->save();


            event(new Registered($user));
            Log::info('Verification email sent to: ' . $user->email);
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful. Please verify your email.',
                ]);
            }

            return redirect()->route('home')->with([
                'status' => 1,
                'message' => 'Registration successful. Please verify your email.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration failed: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed. Please try again.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->route('home')->with([
                'status' => 0,
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function login(Request $request)
    {
        if ($request->expectsJson()) {
            // Validate credentials, etc...

            if (!User::where('email', $request->email)->where('delete_flag', 0)->exists()) {
                return response()->json(['success' => false, 'message' => 'User not found. Please register first.'], 404);
            }

            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if (!$user->hasVerifiedEmail()) {
                    Auth::logout();
                    return response()->json(['success' => false, 'message' => 'Please verify your email first.'], 403);
                }

                $request->session()->regenerate();

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect_url' => $user->role === 'admin' ? route('admin.dashboard') : route('home')
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        // Fallback for non-AJAX:
        return redirect()->route('home')->with([
            'status' => 0,
            'message' => 'Invalid credentials',
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with([
            'status' => 1,
            'message' => 'Logged out successfully',
        ]);
    }


    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('home')->with([
                'status' => 0,
                'message' => 'Invalid verification link.',
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
            Log::info('Email verified for user: ' . $user->email);
        }

        return redirect()->route('home')->with([
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
                return redirect()->route('home')->with([
                    'status' => 1,
                    'message' => 'Verification link sent',
                ]);
            }
            return redirect()->route('home')->with([
                'status' => 0,
                'message' => 'Already verified or not logged in',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend verification email: ' . $e->getMessage());
            return redirect()->route('home')->with([
                'status' => 0,
                'message' => 'Failed to resend verification email.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}