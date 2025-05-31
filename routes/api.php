<?php

// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/dashboard', function (Request $request) {
        return response()->json([
            'message' => 'Admin Dashboard',
            'user' => $request->user(),
        ]);
    })->middleware('role:admin');

    Route::get('/user/dashboard', function (Request $request) {
        return response()->json([
            'message' => 'User Dashboard',
            'user' => $request->user(),
        ]);
    })->middleware('role:user');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});