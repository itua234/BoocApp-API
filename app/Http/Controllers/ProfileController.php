<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\IProfileInterface;
use App\Http\Requests\
{
    SaveNextOfKinDetailsRequest,
    createOrUpdateProfile,
    SavePhoto,
    SaveCoverPhoto
};

class ProfileController extends Controller
{
    protected $profileInterface;

    public function __construct(IProfileInterface $profileInterface)
    {
        $this->profileInterface = $profileInterface;
    }

    public function saveNextOfKinDetails(SaveNextOfKinDetailsRequest $request)
    {
        return $this->profileInterface->saveNextOfKinDetails($request);
    }

    public function saveClientUserDetails(Request $request)
    {
        return $this->profileInterface->saveClientUserDetails($request);
    }
    
    public function saveProfileDetails(createOrUpdateProfile $request)
    {
        return $this->profileInterface->saveProfileDetails($request);
    }

    public function saveProfilePhoto(SavePhoto $request)
    {
        return $this->profileInterface->saveProfilePhoto($request);
    }
    
    public function saveCoverPhoto(SaveCoverPhoto $request)
    {
        return $this->profileInterface->saveCoverPhoto($request);
    }

}
