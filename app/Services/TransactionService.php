<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\WalletResource;
use App\Util\{
    CustomResponse, 
    Paystack, 
    Flutterwave
};
use App\Http\Requests\{
    InitiateDeposit, 
    TransferRequest
};
use Illuminate\Support\Facades\{
    DB, 
    Mail, 
    Hash, 
    Http, 
    Crypt
};
use App\Events\{
    OrderPlaced
};
use App\Models\{
    Transaction, 
    User, 
    Wallet
};

class TransactionService
{
    public function paystackCallback(Request $request)
    {
        $reference = $request['reference'];

        $order = Order::where(['reference' => $reference])->first();
        if (!$order) exit();
        //if ($order->verified) exit();

        $payment = new Paystack;
        $paymentData = $payment->getPaymentData($reference);
        $status = $paymentData['data']["status"];

        $order->payment_status = $status;
       // $order->verified = 1;
        $order->save();

        $subOrders = $order->subOrders;
        foreach($subOrders as $subOrder):
            $owner = $subOrder->user;
            $profile = $owner->profile;
            $total = $subOrder->total;
            
            $profile->orders += 1;
            $profile->sales += $total;
            $profile->save();
            OrderPlaced::dispatch($subOrder);
        endforeach;
    }

    public function flutterwaveCallback(Request $request)
    {
        $transactionId = $request['transaction_id'];
        $reference = $request['tx_ref'];
        $status = $request['status'];
    
        $order = Order::where(['reference' => $reference])->first();
        if (!$order) exit();
        //if ($order->verified) exit();

        $payment = new Flutterwave;
        $response = $payment->verifyTransaction($transactionId);
        
        if($response['data']["status"] === "successful"):
            $order->payment_status = "success";
        else:
            $order->payment_status = "failed";
        endif;
        
        //$order->verified = 1;
        $order->save();

        $subOrders = $order->subOrders;
        foreach($subOrders as $subOrder):
            $owner = $subOrder->user;
            $profile = $owner->profile;
            $total = $subOrder->total;
            
            $profile->orders += 1;
            $profile->sales += $total;
            $profile->save();
            OrderPlaced::dispatch($subOrder);
        endforeach;
    }

    public function paystackTransfer(TransferRequest $request)
    {
        $user = auth()->user();
        $account = $user->bankDetail;
        try{
            $payment = new Paystack;
            $recipient = $payment->createTransferRecipient(
                $account->account_number,
                $account->account_name,
                $account->bank_code
            );

            $reference = Helper::generateReference($user->id);
            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'Debit',
                'amount' => $request['amount'],
                'reference' => $reference,
                'method'  => 'Bank Transfer'
            ]);
            
            return $payment->sendMoney(
                [
                    'recipient' => $recipient['data']["recipient_code"],
                    'amount' => $request['amount'],
                    'reason' => "Workpro Withdrawal Testing thursday",
                    'reference' => $reference
                ]
            );

        }catch(\Exception $e){
            $message = $e->getMessage();
            return CustomResponse::error($message);
        }
    }


    public function transferWebhook(Request $request)
    {
        http_response_code(200);

        $transaction = Transaction::where([
            'reference' => $request['data']["reference"] ])
                ->first();
        if (!$transaction) exit();
        if ($transaction->verified) exit();

        $wallet = Wallet::find($transaction->wallet_id);

        if ($request['event'] == "transfer.success"):
            Transaction::where(['id' => $transaction->id])
            ->update([
                'status' => 'success',
                'verified' => 1
            ]);
            
            $wallet->balance -= $request['data']["amount"] / 100;
            $wallet->save();
        elseif ($request['event'] == "transfer.failed"):
            Transaction::where(['id' => $transaction->id])
            ->update([
                'status' => 'failed',
                'verified' => 1
            ]);
            
            $wallet->available_balance += $request['data']["amount"] / 100;
            $wallet->save();
        elseif ($request['event'] == "transfer.reversed"):
            Transaction::where(['id' => $transaction->id])
            ->update([
                'status' => 'reversed',
                'verified' => 1
            ]);
            
            $wallet->available_balance += $request['data']["amount"] / 100;
            $wallet->save();
        endif;
        
        exit();
    }
    
}
