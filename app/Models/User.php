<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;

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
        'gender',
        'password',
        'user_type',
        'status',
        'latitude',
        'longitude',
        'available',
        'fcm_token',
        'photo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    protected $with = ['wallet'];

    protected $appends = ['profile'];

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

    protected function gender(): Attribute
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

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function profile()
    {
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

    public function getProfileAttribute()
    {
        return $this->profile();
    }

    public function getFullNameAttribute()
    {
        return $this->firstname. " ".$this->lastname;
    }

    public function referral()
    {
        return $this->hasOne(Referral::class, 'user_id');
    }

    public function orders()
    {
        if($this->user_type == 'chef'):
            return $this->chefOrders();
        elseif($this->user_type == 'user'):
            return $this->userOrders();
        else:
            return null;
        endif;
    }

    public function userOrders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function chefOrders()
    {
        return $this->hasMany(Order::class, 'chef_id');
    }

    public function extras()
    {
        return $this->hasMany(DishExtra::class, 'chef_id');
    }

    public function dishes()
    {
        return $this->hasMany(Dish::class, 'chef_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_users', 'user_id', 'service_id');
    } 
}
