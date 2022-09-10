<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\{DeleteUser, SavePhoto};

interface IUserInterface
{
    /*public function saveJobPosts(CreatePost $request);

    public function hire(CreatePost $request);

    public function reviewArtisan(ReviewArtisan $request, $id);

    public function getAllJobCategory();

    public function getArtisansByJobCategory(Request $request, $id);

    public function getAllArtisans(Request $request);

    public function getArtisanDetails($id);*/

    public function delete(DeleteUser $request);

    public function saveProfilePhoto(SavePhoto $request);

    public function saveProfileDetails(Request $request);

    public function getChefsByServiceTypes(Request $request);

    /*public function getUserData();

    public function getPostById($id);

    public function getAdverts();

    public function getActiveJobs();

    public function getArtisanLocation();*/

}