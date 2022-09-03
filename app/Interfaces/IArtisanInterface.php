<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\{
    a,
};

interface IArtisanInterface
{
    public function getJobPosts();
}