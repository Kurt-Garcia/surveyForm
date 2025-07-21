<?php

// Test routes for multi-session authentication
// These routes can be used to verify that multiple guards can be logged in simultaneously

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/test/auth-status', function () {
    $status = [
        'web' => [
            'authenticated' => Auth::guard('web')->check(),
            'user' => Auth::guard('web')->user() ? [
                'id' => Auth::guard('web')->user()->id,
                'name' => Auth::guard('web')->user()->name,
                'email' => Auth::guard('web')->user()->email,
            ] : null,
        ],
        'admin' => [
            'authenticated' => Auth::guard('admin')->check(),
            'user' => Auth::guard('admin')->user() ? [
                'id' => Auth::guard('admin')->user()->id,
                'name' => Auth::guard('admin')->user()->name,
                'email' => Auth::guard('admin')->user()->email,
            ] : null,
        ],
        'developer' => [
            'authenticated' => Auth::guard('developer')->check(),
            'user' => Auth::guard('developer')->user() ? [
                'id' => Auth::guard('developer')->user()->id,
                'username' => Auth::guard('developer')->user()->username,
                'email' => Auth::guard('developer')->user()->email,
            ] : null,
        ],
    ];

    return response()->json($status, 200, [], JSON_PRETTY_PRINT);
})->name('test.auth.status');

Route::get('/test/session-info', function () {
    $sessionData = [
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'cookies' => request()->cookies->all(),
    ];

    return response()->json($sessionData, 200, [], JSON_PRETTY_PRINT);
})->name('test.session.info');