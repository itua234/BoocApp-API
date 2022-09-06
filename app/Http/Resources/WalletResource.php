<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;
use App\Models\Wallet;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $bank = Wallet::find($this->user_id)->bankAccount;
        if($bank):
            $bank->account_name = Crypt::decryptString($bank->account_name);
            $bank->account_number = Crypt::decryptString($bank->account_number);
        endif;
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "balance" => $this->balance,
            "available_balance" => $this->available_balance,
            "bank" => $bank
        ];
    }
}
