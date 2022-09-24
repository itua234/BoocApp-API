<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Route, Auth, DB};
use App\Http\Controllers\{AuthController, 
    ProfileController, DishController,
    WalletController, UserController, OrderController
};

Route::group(['prefix' => 'v1'], function () {
    Route::post("/create-service-types", [UserController::class, "setServiceTypes"]);
    Route::post("/create-roles", [UserController::class, "createRoles"]);

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
        //'prefix' => 'wallet'
    ], function () {
        Route::get("/getbanks", [WalletController::class, "fetchBanks"]);
    });

    Route::group([
        //'prefix' => 'wallet',
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
        Route::post("/save-profile-details", [UserController::class, "saveProfileDetails"]);
        Route::post("/save-profile-photo", [UserController::class, "saveProfilePhoto"]);
        Route::delete("/delete", [UserController::class, "delete"]);
    });

    Route::get("/get-chefs/{Id}", [UserController::class, "getChefsByServiceTypes"]);
    Route::get("/chef/{Id}", [UserController::class, "getChefDetails"]);
    //Route::get("/user/{$Id}", [UserController::class, "getUserDetails"]);

    Route::group([
        'prefix' => 'wallet'
    ], function () {
        Route::get("/get-wallet", [WalletController::class, "getWallet"]);
        Route::get("/get-bank-details/", [WalletController::class, "checkUserBankDetails"]);
        Route::post("/resolve", [WalletController::class, "resolveAccount"]);
        Route::post("/transfer", [WalletController::class, "transfer"]);
    });

    Route::group([
        'prefix' => 'dish'
    ], function () {
        Route::get("/get-categories/{chefId}/", [DishController::class, "getCategories"]);
        Route::get("/get-dishes/{categoryId}/{chefId}/", [DishController::class, "getDishes"]);
        Route::get("/get-extras/{chefId}/", [DishController::class, "getExtras"]);

        Route::post("/create/category", [DishController::class, "createDishCategory"]);
        Route::post("/create/dish-extra", [DishController::class, "addDishAndExtra"]);
        Route::post("/create/dish", [DishController::class, "addDish"]);
        Route::post("/create/extra", [DishController::class, "addExtra"]);
    });

    Route::post("/order", [OrderController::class, "order"]);
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