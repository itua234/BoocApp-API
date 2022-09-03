<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Util\CustomResponse;
use App\Interfaces\IProfileInterface;
use Illuminate\Support\Facades\{DB, Mail, Http};
use App\Models\{User, UserNextOfKin, UserProfile, Role};
use App\Http\Requests\{
    SaveNextOfKinDetailsRequest,
    createOrUpdateProfile,
    SavePhoto, SaveCoverPhoto
};


class ProfileRepository implements IProfileInterface
{
    public function saveNextOfKinDetails(SaveNextOfKinDetailsRequest $request)
    {
        $user = auth()->user();
        $nextOfKin = UserNextOfKin::updateOrCreate([
            'user_id' => $user->id
        ],[
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'date_of_birth' => $request->date_of_birth,
            'relationship' => $request->relationship,
            'house_address' => $request->house_address
        ]);

        $message = 'Details have been updated Successfully';
        return CustomResponse::success($message, $nextOfKin);
    }

    public function saveClientUserDetails(Request $request)
    {
        $user = auth()->user();
        // $userData = User::updateOrCreate(
        //     [
        //         'user_id' => $user->id
        //     ],[
        //         'firstname' => $request->firstname,
        //         'lastname' => $request->lastname,
        //         'phone_number' => $request->phone_number
        //     ]
        // );

        DB::table('users')->where('id', $user->id)->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone' => $request->phone_number
        ]);

        $user = DB::table('users')->where('id', $user->id)->first();

        $message = "Profile updated Successfully";
        return CustomResponse::success($message, $user);
    }

    public function saveProfileDetails(createOrUpdateProfile $request)
    {
        $user = auth()->user();
       
        $profile = UserProfile::updateOrCreate([
            'user_id' => $user->id
        ],[
            'nationality_id' => isset($request->nationality) ? $request->nationality : null,
            'bio' => isset($request->bio) ? $request->bio : null,
            'date_of_birth' => isset($request->date_of_birth) ? $request->date_of_birth : null,
            'latitude' => isset($request->latitude) ? $request->latitude : null,
            'longitude' => isset($request->longitude) ? $request->longitude : null,
            'town' => isset($request->town) ? $request->town : null,
            'nin' => isset($request->nin) ? $request->nin : null,
            'skill' => isset($request->skill) ? $request->skill : null,
        ]);

        $message = "Profile updated Successfully";
        return CustomResponse::success($message, $profile);
    }

    public function saveProfilePhoto(SavePhoto $request)
    {
        $user = auth()->user();
        try{
            if($request->hasFile('photo')):
                $photo = $request->file('photo');
                $response = \Cloudinary\Uploader::upload($photo);
                $url = $response["url"];
                UserProfile::where(['user_id' => $user->id])
                ->update(['photo' => $url]);
            endif;
            
            return CustomResponse::success("Profile photo path:", $url);
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
    }

    public function saveCoverPhoto(SaveCoverPhoto $request)
    {
        $user = auth()->user();
        try{
            if($request->hasFile('photo')):
                $photo = $request->file('photo');
                $response = \Cloudinary\Uploader::upload($photo);
                $url = $response["url"];
                UserProfile::where(['user_id' => $user->id])
                ->update(['cover_photo' => $url]);
            endif;
            
            return CustomResponse::success("Cover photo path:", $url);
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
    }

}