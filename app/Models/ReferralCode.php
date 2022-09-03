<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    use HasFactory, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'code'
    ];


    public function redeem($userID)
    {
        /*$owner = User::with('wallet')->find($this->user_id);

        $prevCredit = 0;

        if ($owner->goCredit != null) {
            $prevCredit = $owner->goCredit->credits;
        }

        $newCredits = $prevCredit + 100;

        $owner->wallet()->update(
            ['credits' => $newCredits]
        );
        Wallet::where(['user_id' => $this->user_id])
        ->update([
            'balance' => 
        ])
        */

        ReferralCodeUsage::create([
            'redeemer_id' => $userID,
            'owner_id' => $this->user_id
        ]);
    }


}
