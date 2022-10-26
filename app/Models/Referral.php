<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'code',
        'type',
        'earnings'
    ];

    protected $hidden = [
        'user_id', 
        'created_at',
        'updated_at',
        'type'
    ];

    protected $with = ['payouts'];

    public function redeem($userID)
    {
        $owner = Referral::where('user_id', $this->user_id)->first();

        $prevBalance = $owner->earnings;
        $newBalance = $prevBalance + 500;

        $owner->earnings = $newBalance;
        $owner->save();

        ReferralCodeUsage::create([
            'redeemer_id' => $userID,
            'owner_id' => $this->user_id
        ]);
    }

    public function payouts()
    {
        return $this->hasMany(EarningPayout::class)
        ->orderBy('updated_at', 'DESC');
    }


}
