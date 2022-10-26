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

    public function updateProfilePhoto(Request $request)
    {
        return $this->userInterface->updateProfilePhoto($request);
    }

    public function updateProfileData(Request $request)
    {
        return $this->userInterface->updateProfileData($request);
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

    public function  getAllChefsByService(Request $request, $id)
    {
        return $this->userInterface-> getAllChefsByService($request, $id);
    }

    public function getAllChefs()
    {
        return $this->userInterface->getAllChefs();
    }

    public function fetchReferralData($userId)
    {
        return $this->userInterface->fetchReferralData($userId);
    }

    public function withdrawReferralEarnings(Request $request)
    {
        return $this->userInterface->withdrawReferralEarnings($request);
    }

    public function fetchReports($userId)
    {
        return $this->userInterface->fetchReports($userId);
    }
    
}
