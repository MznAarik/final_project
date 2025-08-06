<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function showProfile()
{
    $user = auth()->user(); // get logged-in user
    return view('profile', compact('user'));
}
public function updateProfile(Request $request)
{
    $user = auth()->user();

    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'phoneno' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'province_id' => 'nullable|exists:provinces,id',
        'district_id' => 'nullable|exists:districts,id',
        // Add validation rules for other fields like gender, date_of_birth, etc.
    ]);

    $user->update($validatedData);

    return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderByDesc('created_at')->where('delete_flag', '!=', true)->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    public function updateRole(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $request->validate([
                'role' => 'required|in:user,admin',
            ]);

            $user->update([
                'role' => $request->role,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);
        } catch (Exception $e) {
            Log::error('Role update error:' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!']);
        }

        return response()->json(['message' => 'Role updated successfully'], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phoneno' => 'nullable|string|max:10',
            ]);

            $user->update(array_merge(
                $request->only(['name', 'email', 'phoneno']),
                ['updated_by' => Auth::id()]
            ));
        } catch (Exception $e) {
            Log::error('Update error:' . $e->getMessage());
            return response()->json(['message' => ' Something went wrong!']);
        }
        return response()->json(['message' => 'User updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update([
                'delete_flag' => true,
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (Exception $e) {
            Log::error('Deletion error:' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!']);
        }
    }
}
