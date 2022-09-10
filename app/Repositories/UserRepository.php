<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{User, Role, UserProfile, ChefProfile};
use App\Util\CustomResponse;
use App\Services\FCMService;
use App\Interfaces\IUserInterface;
use Illuminate\Support\Facades\{DB, Http};
use App\Http\Requests\{DeleteUser, SavePhoto};
use App\Http\Resources\{UserResource, ChefResource};


class UserRepository implements IUserInterface
{
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

    public function saveProfilePhoto(SavePhoto $request)
    {
        $user = auth()->user();
        try{
            if($request->hasFile('photo')):
                $photo = $request->file('photo');
                $response = \Cloudinary\Uploader::upload($photo);
                $url = $response["url"];
                $user->profile_photo_path = $url;
                $user->save();
            endif;
            
            return CustomResponse::success("Profile photo path:", $url);
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
    }
    
    public function saveProfileDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname'   =>  "required|max:50",
            'lastname'     =>  "required|max:70",
            'phone'     =>  "required|numeric|min:11|unique:users,phone",
            'address' => "",
            'city' => "",
            'state' => ""
        ]);
        if($validator->fails()):
            return response([
                'message' => $validator->errors()->first(),
                'error' => $validator->getMessageBag()->toArray()
            ], 422);
        endif;

        $user = auth()->user();
        $data = User::where(['id' => $user->id])
        ->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone' => $request->phone,
        ]);

        $profile = $user->profile()->updateOrCreate([
            'user_id' => $user->id
        ],[
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state
        ]);

        $message = "Profile updated Successfully";
        return CustomResponse::success($message, $data);
    }

    public function getChefsByServiceTypes(Request $request)
    {
        return auth()->user();
    }
}