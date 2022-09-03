<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Util\CustomResponse;
use App\Http\Resources\UserResource;
use App\Http\Requests\{LoginRequest, VerifyAccount, 
    ResetPassword, ChangePassword, CreateUser};
use App\Mail\{VerifyAccountMail, ForgetPasswordMail};
use Illuminate\Support\Facades\{DB, Mail, Hash, Http, Socialite};
use App\Actions\Fortify\{CreateNewUser, ResetUserPassword};
use App\Models\{User, Wallet, Role, UserProfile, ChefProfile, PasswordReset};


class AuthService
{
    public function login(LoginRequest $request)
    {
        try{
            $user = User::where("email", $request->email)->first();
            $role = Role::find($request->role_id);
            if(!$user || !password_verify($request->password, $user->password)):
                $message = "Wrong credentials";
                return CustomResponse::error($message, 400);
            elseif((int)$user->is_verified !== 1):
                $message = "Email address not verified, please verify your email before you can login";
                return CustomResponse::error($message, 401);
            elseif(!$user->hasRole($role->name)):
                $message = "You are not permitted";
                return CustomResponse::error($message, 401);
            endif;
            
            $token = $user->createToken("ChefAnywhere")->plainTextToken;
            $user->token = $token;
            $message = 'Login successfully';
            return CustomResponse::success($message, $user);
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
    }

    public function register(CreateUser $request)
    {
        try{
            $createUser = new CreateNewUser;
            $user = $createUser->create($request->input());

            $token = $user->createToken("ChefAnywhere")->plainTextToken;
            $user->token = $token;
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }

        $message = 'Thanks for signing up! Please check your email to complete your registration.';
        return CustomResponse::success($message, $user, 201);
    }

    public function requestTokenGoogle(Request $request)
    {
        //Getting the user from socialite using token from google
        $user = Socialite::driver('google')->stateless()->userFromToken($request->token);

        //Getting or creating user from db
        $userFromDb = User::firstOrCreate([
            'email' => $user->getEmail()
        ],[
            'email_verified_at' => Carbon::now(),
            'is_verified' => 1,
            'status' => '1',
            'firstname' => $user->offsetGet('given_name'),
            'lastname' => $user->offsetGet('family_name'),
            'phone' => NULL
        ]);

        $token = $userFromDb->createToken("ChefAnywhere")->plainTextToken;
        $userFromDb->token = $token;
        $message = 'Google Login successful';
        return CustomResponse::success($message, $userFromDb);
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return CustomResponse::success("User has been logged out", null);
    }

    public function refresh()
    {
        $user = auth()->user();

        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });

        $token = $user->createToken("workpro")->plainTextToken;

        return CustomResponse::success("token refreshed successfully", $token);
    }

    public function sendverificationcode($email)
    {
        $user = User::where(['email' => $email])->first();
        
        try{
            $code = mt_rand(1000, 9999);
            DB::table('user_verification')
            ->where(['email' => $user->email])
            ->update([
                'code' => $code, 
                'expiry_time' => Carbon::now()->addMinutes(6)
            ]);

            Mail::to($user->email)
                ->send(new VerifyAccountMail($user, $code));
            $message = 'A new verification code has been sent to your email.';
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
        return CustomResponse::success($message, null);
    }

    public function verifyUser(VerifyAccount $request)
    {
        $check = DB::table('user_verification')
        ->where([
            'email' => $request->email, 
            'code' => $request->code
        ])->first();
        $current_time = Carbon::now();
        try{
            switch(is_null($check)):
                case(false):
                    if($check->expiry_time < $current_time):
                        $message = 'Verification code is expired';
                    else:
                        $user = User::where('email', $check->email)->first();
                        User::where('id', $user->id)
                        ->update([
                            'is_verified' => 1, 
                            'email_verified_at' => $current_time
                        ]);

                        DB::table('user_verification')
                        ->where('email', $request->email)->delete();

                        $message = 'Your email address is verified successfully.';
                        return CustomResponse::success($message, null);
                    endif;
                break;
                default:
                    $message = "Verification code is invalid.";
            endswitch;
        }catch(\Exception $e){
            $error_message = $e->getMessage();
            return CustomResponse::error($error_message);
        }
        return CustomResponse::error($message);
    }

    public function verifyUserThroughWeb(VerifyAccount $request)
    {
        $check = DB::table('user_verification')
        ->where([
            'email' => $request->email, 
            'code' => $request->code
        ])->first();
        $current_time = Carbon::now();

        switch(is_null($check)):
            case(false):
                if($check->expiry_time < $current_time):
                    $message = 'Verification code is expired';
                else:
                    $user = User::where('email', $check->email)->first();
                    User::where('id', $user->id)
                    ->update([
                        'is_verified' => 1, 
                        'email_verified_at' => $current_time
                    ]);

                    DB::table('user_verification')
                    ->where('email', $request->email)->delete();

                    $message = 'Your email address is verified successfully.';
                    return CustomResponse::success($message, null);
                endif;
            break;
            default:
                $message = "Verification code is invalid.";
        endswitch;
        return CustomResponse::error($message);
    }

    public function resetPassword(ResetPassword $request)
    {
        $user = User::where(['email' => $request->email])->first();
        $token = mt_rand(1000, 9999);
        $expiry_time = Carbon::now()->addMinutes(6);

        try{
            PasswordReset::updateOrCreate([
                'email' => $user->email
            ],[
                'token' => $token,
                'expiry_time' => $expiry_time
            ]);
            $message = 'A password reset email has been sent! Please check your email.';    

            Mail::to($user->email)
                ->send(new ForgetPasswordMail($user, $token));
        }catch(\Exception $e){
            $error_message = $e->getMessage();
            return CustomResponse::error($error_message);
        }

        return CustomResponse::success($message, null);
    }

    public function verifyResetToken(Request $request)
    {
        $validator = Validator::make($request, [
            'email' => 'required|email',
            'token' => 'required|numeric|exists:password_resets'
        ]);

        $tokenedUser = DB::table('password_resets')
        ->where([
            'token' => $request->token, 
            'email' => $request->email
        ])->first();

        if(!is_null($tokenedUser)):
            if($tokenedUser->expiry_time > Carbon::now()):
                return view('auth.password-reset', [
                    'email' => $request->email
                ]);
            endif;
        endif;
    }

    public function password_reset(Request $request)
    {   
        try{
            $user = User::where(['email' => $request->email])->first();
            $resetUser = new ResetUserPassword;
            $reset = $resetUser->reset($user, $request->input());

            $message = 'Your password has been changed!';
        }catch(\Exception $e){
            $error_message = $e->getMessage();
            return CustomResponse::error($error_message);
        }

        return CustomResponse::success($message, null);
    }

    public function change_password(ChangePassword $request)
    {
        $user = auth()->user();
        try{
            if((Hash::check($request->current_password, $user->password)) == false):
                $message = "Check your old password.";
            elseif((Hash::check($request->password, $user->password)) == true):
                $message = "Please enter a password which is not similar to your current password.";
            else:
                $user->password = $request->password;
                $user->save();

                $message = "Your password has been changed successfully";
                return CustomResponse::success($message, null);
            endif;
        }catch(\Exception $e){
            $error_message = $e->getMessage();
            return CustomResponse::error($error_message);
        }
        
        return CustomResponse::error($message, 400);
    }

    public function saveFCMToken(Request $request)
    {
        $user = auth()->user();
        try{
            $user->fcm_token = $request->token;
            $user->save();

            $message = 'FCM token updated successfully';
        }catch(\Exception $e){
            $error_message = $e->getMessage();
            return CustomResponse::error($error_message);
        }
        return CustomResponse::success($message, null);
    }

    public function createAdmin(CreateUser $request)
    {
        try{
            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'is_user' => ($request->user_type == 'user') ? 1 : 0,
                'is_chef' => ($request->user_type == 'chef') ? 1 : 0,
                'is_admin' => ($request->user_type == 'admin') ? 1 : 0,
                'status' => ($request->user_type == 'chef') ? '0' : '1'
            ]);
            
            $user->attachRole($request->user_type);
            
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }

        $message = 'successful';
        return CustomResponse::success($message, $user, 201);
    }
}
