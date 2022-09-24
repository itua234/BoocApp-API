<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{User, Role, UserProfile, ChefProfile, Services};
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
        $user = auth()->user();
        $user->firstname = $request['user']["firstname"];
        $user->lastname = $request['user']["lastname"];
        $user->phone = $request['user']["phone"];
        $user->save();

        $profile = $user->profile()->updateOrCreate([
            'user_id' => $user->id
        ],[
            'address' => $request['profile']["address"],
            'city' => $request['profile']["city"],
            'state' => $request['profile']["state"]
        ]);

        $data = User::with('profile')->where('id', $user->id)->first();
        $message = "Profile updated Successfully";
        return CustomResponse::success($message, $data);
    }

    public function getChefsByServiceTypes(Request $request, $Id)
    {
        $lists = DB::table('service_user')
        ->where('service_id', $Id)->get(); 
        return $lists;
    }

    public function getChefDetails($Id)
    {
        $chef = User::with('wallet')
        ->where(['id' => $Id])->first();
        return $chef;
    }
}