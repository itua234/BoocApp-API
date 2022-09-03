<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\{
    SaveNextOfKinDetailsRequest,
    createOrUpdateProfile,
    SavePhoto,
    SaveCoverPhoto
};

interface IProfileInterface
{
    public function saveNextOfKinDetails(SaveNextOfKinDetailsRequest $request);

    public function saveProfileDetails(createOrUpdateProfile $request);

    public function saveProfilePhoto(SavePhoto $request);
    
    public function saveCoverPhoto(SaveCoverPhoto $request);

    public function saveClientUserDetails(Request $request);

}