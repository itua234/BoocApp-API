<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\
{
    User, Job, JobPost
};
use App\Util\CustomResponse;
use App\Interfaces\IArtisanInterface;
use Illuminate\Http\Request;
use App\Http\Requests\
{
    a,
};


class ArtisanRepository implements IArtisanInterface
{
    
    public function getJobPosts()
    {
        $posts = JobPost::all();
        return CustomResponse::success("successful", $posts);
    }
}