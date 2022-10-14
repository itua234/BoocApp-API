<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\{
    DeleteUser, 
    SavePhoto
};

interface IUserInterface
{
    public function storeFcmToken(Request $request);

    public function delete(DeleteUser $request);

    public function updateProfilePhoto(SavePhoto $request);

    public function updateProfileData(Request $request);

    public function getChefsByServiceTypes(Request $request, $Id);

    public function getUserData($userId);

    public function newsletter(Request $request);

    public function sendPushNotification(Request $request);

    public function updateAddressInfo(Request $request);

    public function chefVerification(Request $request);

}