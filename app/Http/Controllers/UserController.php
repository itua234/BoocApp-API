<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Interfaces\IUserInterface;
use Illuminate\Support\Facades\{DB, Http};
use App\Http\Requests\{
    DeleteUser, SavePhoto
};

class UserController extends Controller
{
    protected $userInterface;

    public function getRandomUsers()
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/users');
        return json_decode($response->body());
    }
     
    public function getUserDetails($user, $type)
    {
        $_user =  array_rand($user, 1);
        $_plus = rand();
        
        if($type == 'name'):
            $names = explode(" ", $user[$_user]->name);
            return $names[0];
        elseif($type == 'email'):
            return $user[$_user]->email . (string)$_plus;
        elseif($type == 'phone_number'):
            return $user[$_user]->phone . (string)$_plus;
        elseif($type == 'bio'):
            return $user[$_user]->company->catchPhrase;
        elseif($type == 'lat'):
            return $user[$_user]->address->geo->lat;
        elseif($type == 'lng'):
            return $user[$_user]->address->geo->lng;
        endif;
    }
 
    public function createArtisans(Request $request)
    {
        $users = $this->getRandomUsers();

        $phone_number = $this->getUserDetails($users,'phone_number');
        $firstname = $this->getUserDetails($users, 'name');
        $lastname = $this->getUserDetails($users,'name');
        $longitude = $this->getUserDetails($users,'lng');
        $latitude = $this->getUserDetails($users,'lat');
        $email = $this->getUserDetails($users,'email');
        $bio = $this->getUserDetails($users,'bio');
        $password = rand(0,8);
        $nin = rand();

        $skill = $this->getSkill($request->job_id);
        $job_id = $request->job_id;
        $role_id = 2;
        $photo = $request->photo;

        $request = new Request();
        $request->replace(
            [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'phone' => $phone_number,
                'password' => $password,
                'password_confirmation' => $password,
                'role_id' => $role_id,
                'nin' => $nin,
                'job_id' => $job_id,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'bio' => $bio,
                'photo' => $photo,
                'skill' => $skill
            ]
        );

        app('App\Http\Controllers\AuthController')->store($request);
        return response()->json([
            'data' =>  $firstname . ' ' . $lastname
        ]);
    }

    public function postDishCategory(Request $request){
        foreach($request->jobs as $job){
            DB::table('jobs')->insert(["job_type" => $job]);
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

    public function __construct(IUserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function delete(DeleteUser $request)
    {
        return $this->userInterface->delete($request);
    }

    
}
