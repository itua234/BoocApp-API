<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\{
    DeleteUser, 
};

interface IUserInterface
{
    public function storeFcmToken(Request $request);

    public function delete(DeleteUser $request);

    public function updateProfilePhoto(Request $request);

    public function updateProfileData(Request $request);

    public function getUserData($userId);

    public function newsletter(Request $request);

    public function sendPushNotification(Request $request);

    public function updateAddressInfo(Request $request);

    public function chefVerification(Request $request);

    public function getAllChefsByService(Request $request, $id);

    public function getAllChefs();

    public function filterChefs();

    public function reviewChef(Request $request);

    public function reviewClient(Request $request);

    public function fetchReferralData($userId);

    public function fetchReports($userId);
}