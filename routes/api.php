<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, ProfileController,
    WalletController, UserController};

Route::group(['prefix' => 'v1'], function () {
    /*Route::post("/postJobCategory", [UserController::class, "postJobCategory"]);
    Route::post("/create_chefs", [UserController::class, "createChef"]);*/
    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::post("/register", [AuthController::class, "register"]);
        Route::post("/login", [AuthController::class, "login"]);
        Route::post("/google/login", [AuthController::class, "requestTokenGoogle"]);
        Route::post("/sendcode/{email}", [AuthController::class, "sendcode"]);
        Route::post("/email/verify/", [AuthController::class, "verifyUser"]);
        Route::post("/password/reset", [AuthController::class, "resetPassword"]);
        Route::post("/reset-password", [AuthController::class, "password_reset"]);
    });

    Route::group([
        'prefix' => 'wallet'
    ], function () {
        Route::get("/getbanks", [WalletController::class, "fetchBanks"]);
    });

    Route::group([
        'prefix' => 'user'
    ], function () {
        Route::post("/create-roles", [UserController::class, "createRoles"]);
    });

    Route::group([
        'prefix' => 'wallet',
        'middleware' => ['verify.paystack']
    ], function () {
        Route::post("/transfer/webhook", [WalletController::class, "transferWebhook"]);
    });
});


//protected route using Laravel Sanctum
Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']],function(){
    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::get("/logout", [AuthController::class, "logout"]);
        Route::post("/refresh", [AuthController::class, "refresh"]);
        Route::post("/change-password", [AuthController::class, "change_password"]);
        Route::post("/save-fcm-token", [AuthController::class, "saveFCMToken"]);
    });
    
    Route::group([
        'prefix' => 'user'
    ], function () {
        Route::post("/save-profile-details", [ProfileController::class, "saveProfileDetails"]);
        Route::post("/save-client-profile-details", [ProfileController::class, "saveClientUserDetails"]);
        Route::post("/save-profile-photo", [ProfileController::class, "saveProfilePhoto"]);
    });

    Route::group([
        'prefix' => 'wallet'
    ], function () {
        Route::get("/get-wallet", [WalletController::class, "getWallet"]);
        Route::get("/get-bank-details/", [WalletController::class, "checkUserBankDetails"]);
        Route::post("/resolve", [WalletController::class, "resolveAccount"]);
        Route::post("/transfer", [WalletController::class, "transfer"]);
    });

});
