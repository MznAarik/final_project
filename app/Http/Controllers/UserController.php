<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderByDesc('created_at')->get();
        // foreach ($users as $user) {
        //     $updatedUser = User::where($user->updated_by, 'id')->value('name');
        // }
        // dump($updatedUser);
        return view('admin.users.index', compact('users'));
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
            Log::error('Deletion error:', $e->getMessage());
            return response()->json(['message', ' Something went wrong!']);
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

            $user->update($request->only(['name', 'email', 'phoneno']));
            $user->update(['updated_by' => Auth::id(), 'updated_at' => now()]);
        } catch (Exception $e) {
            Log::error('Deletion error:', $e->getMessage());
            return response()->json(['message', ' Something went wrong!']);
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
            $user->delete();

        } catch (Exception $e) {
            Log::error('Deletion error:', $e->getMessage());
            return response()->json(['message', ' Something went wrong!']);
        }
        return response()->json(['message', 'User deleted sucessfully'], 200);
    }
}
