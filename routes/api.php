<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Route, DB
};
use App\Http\Controllers\{
        AuthController, 
            UserController,
                WalletController, 
                    DishController, 
                        OrderController
};

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return [
        'app' => 'BoocApp API',
        'version' => '1.0.0',
    ];
});

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

        Route::group(['prefix' => 'password'], function () {
            Route::post('forgot', [AuthController::class, "resetPassword"]);
            Route::put('reset', [AuthController::class, "passwordReset"]);
        });

        Route::get("/logout", [AuthController::class, "logout"])->middleware('auth:sanctum');
        Route::post("/refresh", [AuthController::class, "refresh"])->middleware('auth:sanctum');
        Route::post("/change-password", [AuthController::class, "changePassword"])->middleware('auth:sanctum');
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
    });
});


//protected route using Laravel Sanctum
Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']],function(){
    
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
        Route::get("/{userId}/orders", [OrderController::class, "fetchOrders"]);
        Route::get("/{userId}/reports", [UserController::class, "fetchReports"]);
        Route::get("/{userId}/referral", [UserController::class, "fetchReferralData"]);
        Route::post("/withdraw-earnings", [UserController::class, "withdrawReferralEarnings"]);
    });

    Route::group([
        'prefix' => 'chefs'
    ], function () {
        Route::get("/", [UserController::class, "getAllChefs"]);
        Route::get("/{id}", [UserController::class, "getAllChefsByService"]);
    });

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
        Route::get("/{id}", [OrderController::class, "viewOrder"]);
        Route::post("/", [OrderController::class, "order"]);
        Route::post("/reschedule/{orderId}", [OrderController::class, "rescheduleOrder"]);
        Route::post("/quote-new-price/{orderId}", [OrderController::class, "quoteNewPrice"]);
        Route::post("/accept-or-decline/{orderId}/", [OrderController::class, "acceptOrDeclineOrder"]);
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