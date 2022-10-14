<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{
    User,
    UserProfile, 
    ChefProfile, 
    Service,
    ServiceUser
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
    SavePhoto
};
use App\Http\Resources\{
    UserResource, 
    ChefResource
};

class UserRepository implements IUserInterface
{
    public function storeFcmToken(Request $request)
    {
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

    public function updateProfilePhoto(SavePhoto $request)
    {
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
        $user = auth()->user();
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

    public function getChefsByServiceTypes(Request $request, $Id)
    {
        $lists = ServiceUser::find($Id)->users; 
        return $lists;
    }

    public function getUserData($userId)
    {
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

}