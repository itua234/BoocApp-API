<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'password',
        'user_type',
        'is_verified',
        'fcm_token',
        'profile_photo_path'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    protected function firstname(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => ucwords(strtolower($value)),
        );
    }

    protected function lastname(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => ucwords(strtolower($value)),
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => strtolower($value),
        );
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => bcrypt($value),
        );
    }

    public function wallet(){
        return $this->hasOne(Wallet::class);
    }

    public function profile(){
        if($this->user_type == 'chef'):
            return $this->chefProfile()->first();
            elseif($this->user_type == 'user'):
                return $this->userProfile()->first();
            else:
                return null;
        endif;
    }

    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    } 

    public function chefProfile()
    {
        return $this->hasOne(ChefProfile::class);
    } 

    public function referralCode()
    {
        return $this->hasOne(ReferralCode::class);
    } 

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_user');
    } 
}
