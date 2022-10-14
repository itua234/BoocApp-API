<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Interfaces\IUserInterface;
use Illuminate\Support\Facades\{
    DB, 
    Http
};
use App\Models\{
    Role, 
    Service
};
use App\Http\Requests\{
    DeleteUser, 
    SavePhoto
};

class UserController extends Controller
{
    protected IUserInterface $userInterface;

    public function __construct(IUserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function setServiceTypes(Request $request)
    {
        foreach($request->service as $service){
            Service::create([
                "service_type" => $service
            ]);
        }
        return response()->json(['success'=> true]);
    }

    public function storeFcmToken(Request $request)
    {
        return $this->userInterface->storeFcmToken($request);
    }

    public function delete(DeleteUser $request)
    {
        return $this->userInterface->delete($request);
    }

    public function updateProfilePhoto(SavePhoto $request)
    {
        return $this->userInterface->updateProfilePhoto($request);
    }

    public function updateProfileData(Request $request)
    {
        return $this->userInterface->updateProfileData($request);
    }

    public function getChefsByServiceTypes(Request $request, $Id)
    {
        return $this->userInterface->getChefsByServiceTypes($request, $Id);
    }

    public function getUserData($userId)
    {
        return $this->userInterface->getUserData($userId);
    }

    public function newsletter(Request $request)
    {
        return $this->userInterface->newsletter($request);
    }

    public function sendPushNotification(Request $request)
    {
        return $this->userService->sendPushNotification($request);
    }

    public function updateAddressInfo(Request $request)
    {
        return $this->userInterface->updateAddressInfo($request);
    }

    public function chefVerification(Request $request)
    {
        return $this->userInterface->chefVerification($request);
    }
    
}
