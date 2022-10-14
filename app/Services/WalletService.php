<?php 

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Util\{
    CustomResponse, 
    Paystack, 
    Flutterwave
};
use App\Models\{
    Transaction, 
    Wallet, 
    User, 
    BankAccount
};
use App\Http\Requests\{
    ResolveAccount
};
use App\Http\Resources\{
    BankResource
};
use Illuminate\Support\Facades\{
    DB, 
    Http, 
    Crypt, 
    Hash, 
    Mail
};

class WalletService
{
    public function resolveAccount(ResolveAccount $request)
    {
        $wallet = auth()->user()->wallet;
        try{
            $payment = new Paystack;
            $response = $payment->resolve(
                [
                    'account_number' => $request['account_number'],
                    'bank_code' => $request['bank_code']
                ]
            );
            
            if($response['status'] == true):
                $bank = $payment->getBank($request['bank_code']);
                
                $account = $wallet->bankAccount()->updateOrCreate([
                    'wallet_id' => $wallet->id
                ],[
                    'bank_code' => $request['bank_code'],
                    'bank_name' => $bank,
                    'account_number' => $response['data']["account_number"],
                    'account_name' => $response['data']["account_name"]
                ]);
                
                return CustomResponse::success($response['message'], $account);
            else:
                return CustomResponse::error($response['message'], 422);
            endif;

        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
    }

    public function getWallet()
    {
        $wallet = auth()->user()->wallet()->with('bankAccount')->first();
        try{
            return CustomResponse::success('successful', $wallet);
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
    }

    public function fetchBanks()
    {
        try{
            $payment = new Paystack;
            $response = $payment->getBankList();
            
            $data = BankResource::collection($response["data"]);
            return CustomResponse::success('successful', $data);
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
    }

    public function checkUserBankDetails()
    {
        $wallet = auth()->user()->wallet;
        try{
            $bank = $wallet->bankAccount;
            if(!$bank):
                $message = "Account Details not found";
                return CustomResponse::error($message, 404);
            endif;
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
        return CustomResponse::success('Wallet details:', $bank);
    }

}