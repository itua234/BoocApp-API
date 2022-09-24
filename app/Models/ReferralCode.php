<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    use HasFactory;
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'code',
        'type'
    ];

    public function redeem($userID)
    {
        $owner = User::find($this->user_id)->wallet;

        $prevBalance = $owner->referral_earnings;
        $newBalance = $prevBalance + 500;

        $owner->referral_earnings = $newBalance;
        $owner->save();

        ReferralCodeUsage::create([
            'redeemer_id' => $userID,
            'owner_id' => $this->user_id
        ]);
    }


}
