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
use Illuminate\Support\Facades\Session;
use App\Services\CaptchaService;
class AuthController extends Controller
{

    protected $captchaService;

    public function __construct(CaptchaService $captchaService)
    {
        $this->captchaService = $captchaService;
    }

    public function register(UserValidate $userValidate)
    {
        $userValidate->validate([
            'captcha' => 'required|string|6',
        ]);

        $validatedCaptcha = $this->captchaService->verifyCaptcha($userValidate->input('captcha'));
        // Session::forget('captcha');

        if (!$validatedCaptcha) {
            if ($userValidate->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Incorrect captcha. Please retry.'], 400);
            }
        }


        DB::beginTransaction();
        try {
            $role = $userValidate['role'];
            // Check if email is already in use
            if (User::where('email', $userValidate['email'])->exists()) {
                if ($userValidate->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This email is already in use.',
                    ], 409);
                }
                return redirect()->route('home')->with([
                    'status' => 2,
                    'message' => 'This email is already in use.',
                ]);
            }

            // Only allow admins to create admin accounts
            if ($role === 'admin' && (!Auth::check() || Auth::user()->role !== 'admin')) {
                if ($userValidate->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only admins can create admin accounts.',
                    ], 403);
                }
                return redirect()->route('home')->with([
                    'status' => 2,
                    'message' => 'Only admins can create admin accounts.',
                    'error' => 'Unauthorized role assignment',
                ]);
            }
            $country = Country::firstOrCreate(['name' => strtolower($userValidate->input('country_name'))]);
            $province = Province::firstOrCreate(
                ['name' => strtolower($userValidate->input('province_name')), 'country_id' => $country->id]
            );

            $district = District::firstOrCreate(
                ['name' => strtolower($userValidate->input('district_name')), 'province_id' => $province->id],
                ['country_id' => $country->id]
            );


            if (!$district || !$province || !$country) {
                return response()->json(['error' => 'Invalid location data provided.'], 422);
            }

            $user = new User();
            $user->name = strtolower($userValidate->input('name'));
            $user->email = strtolower($userValidate->input('email'));
            $user->password = Hash::make($userValidate['password']);
            $user->gender = strtolower($userValidate->input('gender'));
            $user->phoneno = $userValidate['phoneno'];
            $user->address = strtolower($userValidate->input('address'));
            $user->district_id = $district->id;
            $user->province_id = $province->id;
            $user->country_id = $country->id;
            $user->date_of_birth = $userValidate['date_of_birth'];
            $user->role = $userValidate['role'];
            $user->created_by = Auth::check() ? Auth::id() : null; // Set created_by if logged in
            $user->updated_by = Auth::check() ? Auth::id() : null; // Set updated_by if logged in
            $user->save();


            event(new Registered($user));
            Log::info('Verification email sent to: ' . $user->email);
            DB::commit();

            if ($userValidate->expectsJson()) {
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

            if ($userValidate->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed. Please try again.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->route('home')->with([
                'status' => 2,
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function login(Request $request)
    {
        $request->validate([
            'captcha' => 'required|string|6',
        ]);

        $validatedCaptcha = $this->captchaService->verifyCaptcha($request->input('captcha'));
        // Session::forget('captcha');

        if (!$validatedCaptcha) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Incorrect captcha. Please retry.'], 400);
            }
        }

        if ($request->expectsJson()) {

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

                $message = $user->role === 'admin' ? 'Welcome, admin!' : 'Welcome back, ' . $user->name . '!';

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect_url' => $user->role === 'admin' ? route('admin.dashboard') : route('home')
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        // Fallback for non-AJAX:
        return redirect()->route('home')->with([
            'status' => 2,
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
                'status' => 2,
                'message' => 'Invalid verification link.',
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user->email));
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
                'status' => 2,
                'message' => 'Already verified or not logged in',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend verification email: ' . $e->getMessage());
            return redirect()->route('home')->with([
                'status' => 2,
                'message' => 'Failed to resend verification email.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}