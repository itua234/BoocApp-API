<?php

use Illuminate\Support\Facades\{Route, Auth, DB};
use Illuminate\Http\Request;
use App\Models\{User};
use Carbon\Carbon;
use App\Http\Controllers\{AuthController};

Route::group([
], function () {
    //Route::get('/email/verify', function () {return view('auth.verify-email');})->name('verification.notice');
    Route::get('/forgot-password', function () {return view('auth.forgot-password');})->name('forgot-password');
    Route::get('/reset-password', function (Request $request) {
        return view('auth.reset-password', ['request' => $request]);
    })->name('reset-password');
    
    Route::get('/email', function () {
        return view('email.ver');
    });
    

    Route::get("/verify/reset/{email}/{token}", [AuthController::class, "verifyResetToken"]);
    Route::get("/email/verify/{email}/{code}", [AuthController::class, "verifyUserThroughWeb"]);
});

Route::group([
    'middleware' => ['auth:sanctum']
], function () {
    //Route::get('/', function () {return view('auth.login');});

    Route::get('/dashboard', function () {
        $admin = User::where(['id' => Auth::user()->id])->first();
        return view('dashboard.dashboard',[
            'admin' => $admin, 
        ]);
    })->name('dashboard');

    Route::get('/dashboard/create-admins', function () {
        $admin = User::where(['id' => Auth::user()->id])->first();
        return view('dashboard.create-admins', [
            'admin' => $admin
        ]);
    });

    Route::get('/dashboard/user-profile', function () {
        $admin = User::where(['id' => Auth::user()->id])->first();
        return view('dashboard.user-profile', [
            'admin' => $admin
        ]);
    });

    Route::get('/change-password/', function () {
        return view('auth.change-password');
    });
});


