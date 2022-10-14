<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Route, 
    Auth, 
    DB
};
use App\Http\Controllers\{
    AuthController, 
    UserController,
    WalletController, 
    DishController, 
    OrderController
};

Route::group(['prefix' => 'v1'], function () {
    Route::post("/create-service-types", [UserController::class, "setServiceTypes"]);

    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::post("/register", [AuthController::class, "register"]);
        Route::post("/login", [AuthController::class, "login"]);
        Route::post("/google/login", [AuthController::class, "requestTokenGoogle"]);
        Route::post("/resend-code/{email}", [AuthController::class, "sendcode"]);
        Route::post("/email/verify/{email}/{code}", [AuthController::class, "verifyEmail"]);
        Route::post("/password/reset", [AuthController::class, "resetPassword"]);
        Route::post("/reset-password", [AuthController::class, "passwordReset"]);
    });

    Route::get("/banks", [WalletController::class, "fetchBanks"]);

    Route::group([
        'prefix' => 'callback'
    ], function () {
        //Route::post("/paystack/", [UserController::class, "transferWebhook"]);
        //Route::post("/flutterwave/", [UserController::class, "transferWebhook"]);
    });

    Route::group([
        'prefix' => 'payout',
        'middleware' => ['verify.paystack']
    ], function () {
        //Route::post("/transfer/webhook", [UserController::class, "transferWebhook"]);
        //Route::post("/transfer/webhook", [UserController::class, "transferWebhook"]);
    });
});


//protected route using Laravel Sanctum
Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']],function(){
    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::get("/logout", [AuthController::class, "logout"]);
        Route::post("/refresh", [AuthController::class, "refresh"]);
        Route::post("/change-password", [AuthController::class, "changePassword"]);
    });
    
    Route::group([
        'prefix' => 'user'
    ], function () {
        Route::get("/{userId}", [UserController::class, "getUserData"]);
        Route::post("/profile", [UserController::class, "updateProfileData"]);
        Route::post("/profile/photo", [UserController::class, "updateProfilePhoto"]);
        Route::delete("/delete", [UserController::class, "delete"]);
        Route::post("/fcm-token", [UserController::class, "storeFcmToken"]);
        Route::post("/newsletter", [UserController::class, "newsletter"]);
        Route::post("/send-push-notification", [UserController::class, "sendPushNotification"]);
        Route::post("/update-address-info/", [UserController::class, "updateAddressInfo"]);
        Route::post("/update-chef-info/", [UserController::class, "chefVerification"]);
    });

    Route::get("/get-chefs/{Id}", [UserController::class, "getChefsByServiceTypes"]);

    Route::group([
        'prefix' => 'wallet'
    ], function () {
        Route::get("/", [WalletController::class, "getWallet"]);
        Route::get("/bank-detail/", [WalletController::class, "checkUserBankDetails"]);
        Route::post("/resolve", [WalletController::class, "resolveAccount"]);
        Route::post("/transfer", [WalletController::class, "transfer"]);
    });

    Route::group([
        'prefix' => ''
    ], function () {
        Route::post("dish/", [DishController::class, "addDish"]);
        Route::post("extra/", [DishController::class, "addExtra"]);
        Route::post("/create/dish-extra", [DishController::class, "addDishAndExtra"]);

        Route::post("/category/", [DishController::class, "createDishCategory"]);
        Route::get("{chefId}/categories/", [DishController::class, "getCategories"]);
        Route::get("/{chefId}/{categoryId}/dishes", [DishController::class, "getDishes"]);
        Route::get("/{chefId}/extras/", [DishController::class, "getExtras"]);
    });

    Route::group([
        'prefix' => 'order'
    ], function () {
        Route::get("/{id}", [OrderController::class, "show"]);
        Route::post("/", [OrderController::class, "order"]);
        Route::post("/reschedule/{orderId}", [OrderController::class, "rescheduleOrder"]);
        Route::post("/quote-new-price/{orderId}", [OrderController::class, "quoteNewPrice"]);
    });
});

Route::get("/malone", function(){
    $lat2 = 29.46786;
    $lon2 = -98.53506;
    
    $users = DB::table('users')
    ->select(DB::raw('*, ST_Distance_Sphere(
        point(longitude,latitude),
        point('.$lon2.','.$lat2.')) / 1000 as distance'))
    ->where('id', '<=', 2)
    ->orderBy('distance', 'ASC')
    ->get();
    return $users;
});