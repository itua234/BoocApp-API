<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Util\{
    CustomResponse, 
    Paystack, 
    Flutterwave, 
    Helper
};
use App\Http\Resources\OrderResource;
use App\Http\Requests\{
    LoginRequest
};
use Illuminate\Support\Facades\{
    DB, 
    Http
};
use App\Events\{
    OrderPlaced,
    OrderRescheduled,
    PriceChanged
};
use App\Models\{
    User, 
    Dish, 
    DishExtra, 
    DishCategory, 
    Order,
    OrderContent,
    OrderDetail,
    EarningPayout
};

class OrderService
{  
    public function order(Request $request)
    {
        $user = auth()->user();
        $reference = Helper::generateReference($user->id);
        $orderNo = mt_rand(1000, 9999);
        
        DB::transaction(
            function() use (
                $request, 
                &$order,
                $reference, 
                $orderNo, 
                $user
            ){
            $order = Order::create([
                'user_id' => $user->id,
                'chef_id' => (int) $request['chef_id'],
                'order_no' => $orderNo,
                'type' => strtoupper($request["order_type"]),
                'subtotal' => $request['subtotal'],
                'shipping_cost' => $request['shipping_cost'],
                'subcharge' => $request['subcharge'],
                'total' => $request['total'],
                'reference' => $reference,
                'discount_code' => isset($request['discount_code']) ? $request['discount_code'] : NULL
            ]);

            foreach($request['cart'] as $item):
                OrderContent::create([
                    'order_id' => $order->id,
                    'dish_id' => $item['dish_id'],
                    'dish_quantity' => $item['dish_quantity'],
                    'dish_price' => $item['dish_price'],
                    'extra_id' => $item['extra_id'],
                    'extra_quantity' => $item['extra_quantity'],
                    'extra_price' => $item['extra_price']
                ]);
            endforeach; 
            OrderDetail::create([
                'order_id' => $order->id,
                'date' => $request["details"]['date'],
                'period' => $request["details"]['period'],
                'firstname' => $request["details"]['firstname'],
                'lastname' => $request["details"]['lastname'],
                'phone' => $request["details"]['phone'],
                'address' => $request["details"]['address'],
                'note' => $request["details"]['note'],
                //'is_filled_gas' => $request['is_filled_gas'],
                //'burners' => $request['burners']
            ]);
        });

        //OrderPlaced::dispatch($order);
        return CustomResponse::success("Order:", $order->fresh());
    }

    public function generatePaymentUrl($user, $channel, $total, $reference)
    {
        if($channel === "PAYSTACK"):
            $payment = new Paystack;
            $response = $payment->initiateDeposit(
                $user->email, $total, $reference
            );

            return $response['data']["authorization_url"];
        elseif($channel === "FLUTTERWAVE"):
            $payment = new Flutterwave;
            $response = $payment->initializePayment(
                $user,
                [
                    'tx_ref' => $reference,
                    'amount' => $total,
                ]
            );

            return $response['data']["link"];
        endif;
    }

    public function makePayment(Request $request)
    {
        $order = Order::find($request['id']);
        $user = User::find($order->user_id);
        $channel = strtoupper($request['payment_channel']);
        $order->payment_channel = $channel;
        $order->save();
        $total = $order->total;
        $reference = $order->reference;
        $url = $this->generatePaymentUrl($user, $channel, $total, $reference);

        return CustomResponse::success("Payment Link:", $url);
    }

    public function acceptOrDeclineOrder(Request $request, $orderId)
    {
        $action = $request['action'];
        $reason = $request['reason'];
        $order = Order::find($orderId);
        if($reason):
            $order->reason_for_declining = $reason;
        endif;
        if($action == 'accept'):
            $status = 'accepted';
            elseif($action == 'decline'):
                $status = 'declined';
        endif;
        $order->order_status = $status;
        $order->save();

        $message = "The order with Order No:".$order->order_no." has been ".$status;
        return CustomResponse::success($message, $order->fresh());
    }

    public function rescheduleOrder(Request $request, $orderId)
    {
        $order = Order::find($orderId);
        $order->detail()->update([
            'date' => $request['date'],
            'period' => $request['period']
        ]);

        OrderRescheduled::dispatch($order->fresh());

        $message = "The order rescheduled date has been sent to the customer";
        return CustomResponse::success($message, $order->fresh());
    }

    public function quoteNewPrice(Request $request, $orderId)
    {
        $order = Order::find($orderId);
        $order->update([
            'total' => $request['price'],
        ]);

        PriceChanged::dispatch($order->fresh());

        $message = "A new price has been sent to the customer";
        return CustomResponse::success($message, $order->fresh());
    }

    public function fetchOrders($userId)
    {
        $user = User::find($userId);
        $orders = OrderResource::collection($user->orders);
        /*$order = Order::where('user_id', $userId)
        ->orWhere('chef_id', $userId)->get();*/
        $message = "Orders:";
        return CustomResponse::success($message, $orders);
    }

    public function viewOrder($orderId)
    {
        $user = auth()->user();
        $order = Order::find($orderId);
        $message = "Order Details:";
        return CustomResponse::success($message, $order);
    }
    
}