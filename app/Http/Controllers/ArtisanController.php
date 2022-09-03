<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\IArtisanInterface;
use App\Http\Requests\
{
    a,
};


class ArtisanController extends Controller
{
    protected $artisanInterface;

    public function __construct(IArtisanInterface $artisanInterface)
    {
        $this->artisanInterface = $artisanInterface;
    }

    public function getJobPosts()
    {
        return $this->artisanInterface->getJobPosts();
    }
}
