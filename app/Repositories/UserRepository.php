<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{
    User,
    UserProfile, 
    ChefProfile, 
    Service,
    ServiceUser,
    EarningPayout,
    Referral
};
use App\Util\CustomResponse;
use App\Services\FCMService;
use App\Interfaces\IUserInterface;
use Illuminate\Support\Facades\{
    DB, 
    Http,
    Validator
};
use App\Http\Requests\{
    DeleteUser, 
};
use App\Http\Resources\{
    UserResource, 
    ChefResource,
    ReportResource
};

class UserRepository implements IUserInterface
{
    public function storeFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);
        if($validator->fails()):
            return response([
                'message' => $validator->errors()->first(),
                'error' => $validator->getMessageBag()->toArray()
            ], 422);
        endif;

        $user = auth()->user();
        try{
            $user->fcm_token = $request['token'];
            $user->save();

            $message = 'FCM token updated successfully';
        }catch(\Exception $e){
            $error_message = $e->getMessage();
            return CustomResponse::error($error_message);
        }
        return CustomResponse::success($message, null);
    }

    public function delete(DeleteUser $request)
    {
        $user = auth()->user();
        try{
            if(!password_verify($request->password, $user->password)):
                $message = "Wrong credentials";
                return CustomResponse::error($message, 422);
            endif;

            User::where(['id' => $user->id])->delete();
            $message = 'Account has been deactivated successfully';
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }

        return CustomResponse::success($message, null);
    }

    public function updateProfilePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|mimes:jpeg,jpg,png,svg|max:2048'
        ]);
        if($validator->fails()):
            return response([
                'message' => $validator->errors()->first(),
                'error' => $validator->getMessageBag()->toArray()
            ], 422);
        endif;

        $user = auth()->user();
        $photo = $user->photo;
        if($photo):
            $parts = explode('/', $photo);
            $count = count($parts);
            $publicId = explode('.', $parts[$count - 1]);
            $response = \Cloudinary\Uploader::destroy($publicId[0]);
        endif;
        try{
            if($request->hasFile('photo')):
                $photo = $request->file('photo');
                $response = \Cloudinary\Uploader::upload($photo);
                $url = $response["url"];

                $user->photo = $url;
                $user->save();
            endif;
            
            return CustomResponse::success("Profile photo path:", $url);
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
    }

    public function updateProfileData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user.firstname' => 'required|string',
            'user.lastname' => 'required|string',
            'user.phone' => ['required', 'numeric', 'min:11'],
            'profile.address' => ['string'],
            'profile.state' => ['string'],
            'profile.city' => ['string'],
            'profile.landmark' => ['string'],
        ]);
        if($validator->fails()):
            return response([
                'message' => $validator->errors()->first(),
                'error' => $validator->getMessageBag()->toArray()
            ], 422);
        endif;

        $user = auth()->user();
        $check = User::where('phone', $request['user']['phone'])->first();
        if($check):
            if($user->id !== $check->id):
                $message = "phone number has been used";
                return CustomResponse::error($message, 400);
            endif;
        endif;

        $user->firstname = $request["user"]["firstname"];
        $user->lastname = $request["user"]["lastname"];
        $user->phone = $request["user"]["phone"];
        $user->save();

        $user->profile()->update([
            'address' => $request["profile"]["address"],
            'state' => $request["profile"]["state"],
            'city' => $request["profile"]["city"],
            'nearest_landmark' => $request["profile"]["landmark"],
        ]);
        
        $message = "Profile updated Successfully";
        return CustomResponse::success($message, $user->fresh());
    }

    public function getUserData($userId)
    {
        $validator = Validator::make([
            'userId' => $userId,
        ], [
            'userId' => 'required|integer',
        ]);
        if($validator->fails()):
            return response([
                'message' => $validator->errors()->first(),
                'error' => $validator->getMessageBag()->toArray()
            ], 422);
        endif;

        $user = User::find($userId);
        try{
            //$user = new UserResource($user);
            return CustomResponse::success("successful", $user);
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
    }

    public function newsletter(Request $request)
    {
        DB::table('newsletter')
        ->where(['email' => auth()->user()->email])
        ->update([
            'subscribed' => $request['notify']
        ]);

        if($request['notify'] == 0):
            $message = 'You have unsubscribed from our newsletter';
        elseif($request['notify'] == 1):
            $message = 'You have subscribed for our newsletter';
        endif;
        return CustomResponse::success($message, null);
    } 

    public function sendPushNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver' => 'required|integer',
            'title' => 'required|string',
            'body' => 'required|string',
            'route' => 'required|string'
        ]);
        if($validator->fails()):
            return response([
                'message' => $validator->errors()->first(),
                'error' => $validator->getMessageBag()->toArray()
            ], 422);
        endif;

        $receiver = User::find($request['receiver']);
        FCMService::send(
            $receiver->fcm_token,
            [
                'title' => $request['title'],
                'body' => $request['body'],
                'route' => $request['route']
            ]
        );
            
        return CustomResponse::success('notification haas been sent', null);
    }

    public function updateAddressInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
            'state' => 'required|string',
            'city' => 'required|string',
            'nearest_landmark' => 'required|string'
        ]);
        if($validator->fails()):
            return response([
                'message' => $validator->errors()->first(),
                'error' => $validator->getMessageBag()->toArray()
            ], 422);
        endif;

        $user = auth()->user();
        $user->profile()->update([
            'address' => $request["address"],
            'state' => $request["state"],
            'city' => $request["city"],
            'nearest_landmark' => $request["nearest_landmark"],
            //'video_verification_url' => $request["profile"]["video"]
        ]);
        
        $message = "Address updated Successfully";
        return CustomResponse::success($message, $user->fresh());
    }

    public function chefVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image.*' => 'required|mimes:jpeg,jpg,png,svg|max:2048',
            'is_certified'     => "required|integer",
            'is_restaurant' => 'required|integer',
            'cac_reg_number' => 'nullable',
            'restaurant_name' => 'nullable|string',
            'restaurant_address' => 'nullable|string',
            'home_service' => 'integer',
            'occasion_service' => 'integer'
        ]);
        if($validator->fails()):
            return response([
                'message' => $validator->errors()->first(),
                'error' => $validator->getMessageBag()->toArray()
            ], 422);
        endif;

        $user = auth()->user();
        $user->gender = $request['gender'];
        $user->save();

        $urls = array();
        if($request->hasFile('image')):
            $image = $request->file('image');
            foreach($image as $photo):
                $response = \Cloudinary\Uploader::upload($photo);
                $url = $response["url"];
                array_push($urls, $url);
            endforeach;
        endif;

        $user->profile()->update([
            'id_card_url' => $urls[0],
            'video_url' => $urls[1],
            'is_certified' => $request['is_certified'],
            'certificate_url' => ((int)$request['is_certified'] === 1) ? $urls[2] : NULL,
            'is_restaurant' => $request['is_restaurant'],
            'cac_reg_number' => ((int)$request['is_restaurant'] === 1) ? $request['cac_reg_number'] : NULL,
            'restaurant_name' => ((int)$request['is_restaurant'] === 1) ? $request['restaurant_name'] : NULL,
            'restaurant_address' => ((int)$request['is_restaurant'] === 1) ? $request['restaurant_address'] : NULL,
        ]);
        $user->services()->attach(1);
        if($request['home_service'] == 1):
            $user->services()->attach(2);
        endif;
        if($request['occasion_service'] == 1):
            $user->services()->attach(3);
        endif;

        $message = "Profile updated Successfully";
        return CustomResponse::success($message, $user->fresh());
    }

    public function getAllChefsByService(Request $request, $id)
    {
        $validator = Validator::make([
            'id' => $id,
        ], [
            'id' => 'required|integer',
        ]);
        if($validator->fails()):
            return response([
                'message' => $validator->errors()->first(),
                'error' => $validator->getMessageBag()->toArray()
            ], 422);
        endif;
        
        $users = Service::find($id)->users;
        $users = $users->load('dishes'); 
        $users = ChefResource::collection($users);
        $message = "Chefs:";
        return CustomResponse::success($message, $users);
    }

    public function getAllChefs()
    {
        $users = User::where(['user_type' => 'chef', 'status' => 'active'])
        ->with('dishes', 'services')->get();
        $users = ChefResource::collection($users);
        $message = "Chefs:";
        return CustomResponse::success($message, $users);
    }

    public function filterChefs()
    {
        $users = User::where([
            'user_type' => 'chef'
        ])->with('dishes')->get();
        $users = ChefResource::collection($users);
        $message = "Chefs:";
        return CustomResponse::success($message, $users);
    }

    public function reviewChef(Request $request)
    {
        $user = auth()->user();
        $chef = User::find($request['chefId']);
        $review = ChefReview::create([
            'user_id' -> $user->id,
            'chef_id' => $request['chefId'],
            'text' => isset($request['text']) ? $request['text'] : NULL,
            'rating' => $request['rating']
        ]);

        /*$user->profile()->update([
            'rating' => $rating
        ]);*/
        $message = "Review:";
        return CustomResponse::success($message, $review);
    }

    public function reviewClient(Request $request)
    {
        $user = auth()->user();
        $review = UserReview::create([
            'chef_id' -> $user->id,
            'user_id' => $request['userId'],
            'text' => $request['text'],
            'remark' => $request['remark']
        ]);
        $message = "Review:";
        return CustomResponse::success($message, $review);
    }

    public function fetchReferralData($userId)
    {
        $user = User::find($userId);
        $referral = $user->referral;
        $message = "Referral Details:";
        return CustomResponse::success($message, $referral);
    }

    public function withdrawReferralEarnings(Request $request)
    {
        $user = auth()->user();
        $referral = Referral::where('user_id', $user->id)->first();

        if($referral->earnings > 0):
            $payout = EarningPayout::create([
                'referral_id' => $referral->id,
                'amount' => $referral->earnings,
            ]);
        else:
            $message = "Insufficient Balance";
            return CustomResponse::error($message, 400);
        endif;

        $message = "A withdrawal request has been sent";
        return CustomResponse::success($message, $payout);
    }

    public function fetchReports($userId)
    {
        $user = User::find($userId);
        $orders = ReportResource::collection($user->orders);
        $message = "Reports:";
        return CustomResponse::success($message, $orders);
    }
}