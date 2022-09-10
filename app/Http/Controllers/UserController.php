<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Interfaces\IUserInterface;
use Illuminate\Support\Facades\{DB, Http};
use App\Models\{User, Role, UserProfile, ChefProfile};
use App\Http\Requests\{
    DeleteUser, SavePhoto
};

class UserController extends Controller
{
    protected IUserInterface $userInterface;

    public function __construct(IUserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function setDishCategories(Request $request){
        foreach($request->category as $category){
            DB::table('dish_categories')
            ->insert([
                "name" => $category
            ]);
        }
        return response()->json(['success'=> true]);
    }

    public function setServiceTypes(Request $request){
        foreach($request->service as $service){
            DB::table('services')
            ->insert([
                "service_type" => $service,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        return response()->json(['success'=> true]);
    }

    public function createRoles(Request $request){
        foreach($request->roles as $role){
            DB::table('roles')
            ->insert([
                "name" => $role,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        return response()->json(['success'=> true]);
    }

    public function delete(DeleteUser $request)
    {
        return $this->userInterface->delete($request);
    }

    public function getChefsByServiceTypes(Request $request)
    {
        return $this->userInterface->getChefsByServiceTypes($request);
    }

    
}
