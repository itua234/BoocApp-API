<?php

namespace App\Actions\Fortify;

use Carbon\Carbon;
use App\Util\Helper;
use App\Mail\VerifyAccountMail;
use App\Models\{User, Wallet, Role, UserProfile, ChefProfile, ReferralCode};
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\{DB, Mail};

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'password' => $input['password'],
                'user_type' => $input['user_type'],
                'is_verified' => ($input['user_type'] == 'chef') ? '0' : '1'
            ]), function (User $user) use ($input) {
                $user->attachRole($input['user_type']);

                if($input['user_type'] != "admin"):

                    $user->referralCode()->create([
                        'code' => Helper::generateReferral($user->firstname),
                        'type' =>  ($input['user_type'] == 'chef') ? 'chef' : 'user'
                    ]);

                    Wallet::create([
                        'user_id' => $user->id
                    ]);

                    if(isset($input['referral_code'])):
                        $code = ReferralCode::where([
                            'code' => $input['referral_code'
                        ]])->first();
                        if($code):
                            if($code->type == $input['user_type']):
                                $redeemRes = $code->redeem($user->id);
                            endif;
                        endif;
                    endif;

                    $code = mt_rand(1000, 9999);
                    DB::table('user_verification')
                    ->insert([
                        'email' => $user->email, 
                        'code' => $code, 
                        'expiry_time' => Carbon::now()->addMinutes(6)
                    ]);

                    DB::table('newsletter')
                    ->insert([
                        'email' => $user->email
                    ]);

                    /*Mail::to($user->email)
                        ->send(new VerifyAccountMail($user, $code));*/
                endif;
            });
        });
    }
}
