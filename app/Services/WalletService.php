<?php 

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Util\{CustomResponse, Paystack, Flutterwave};
use App\Models\{Transaction, Wallet, User, BankAccount};
use App\Http\Requests\{ResolveAccount};
use App\Http\Resources\{BankResource, WalletResource};
use Illuminate\Support\Facades\{DB, Http, Crypt, Hash, Mail};

class WalletService
{
    public function resolveAccount(ResolveAccount $request)
    {
        $wallet = auth()->user()->wallet;
    
        try{
            $payment = new Paystack;
            $response = $payment->resolve(
                [
                    'account_number' => $request->account_number,
                    'bank_code' => $request->bank_code
                ]
            );
            
            if($response['status'] == true):
                $bank = $payment->getBank($request->bank_code);
                
                $account = $wallet->bankAccount()->create([
                    'bank_code' => $request->bank_code,
                    'bank_name' => $bank,
                    'account_number' => Crypt::encryptString($response['data']["account_number"]),
                    'account_name' => Crypt::encryptString($response['data']["account_name"])
                ]);

                $account->account_number = Crypt::decryptString($account->account_number);
                $account->account_name = Crypt::decryptString($account->account_name);
                
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
        $wallet = auth()->user()->wallet;
        try{
            if($wallet->has_bank_details):
                $wallet->account_name = Crypt::decryptString($wallet->account_name);
                $wallet->account_number = Crypt::decryptString($wallet->account_number);
            endif;
           
            $transactions = Wallet::find($wallet->id)->transactions()
                ->orderBy('updated_at', 'DESC')
                    ->get();
            foreach($transactions as $array):
                $array->amount = number_format($array->amount);
                $array->updated = $array->updated_at->toFormattedDateString();
                unset($array->updated_at);
            endforeach;
        
            $wallet->transactions = $transactions;
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
        $user = auth()->user();
        $wallet = User::find($user->id)->wallet;
        try{
            if(!$wallet->has_bank_details):
                $message = "Account Details not found";
                return CustomResponse::error($message, 404);
            endif;
        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
        return CustomResponse::success('Wallet details:', new WalletResource($wallet));
    }

}