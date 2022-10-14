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
//use App\Http\Resources\UserResource;
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
    OccasionServiceDetail,
    DeliveryServiceDetail
};

class OrderService
{  
    public function order(Request $request)
    {
        $user = auth()->user();
        $total = $request['total'];
        $reference = Helper::generateReference($user->id);
        $orderNo = mt_rand(1000, 9999);
        
        $order = Order::create([
            'user_id' => $user->id,
            'chef_id' => (int) $request['chef_id'],
            'order_no' => $orderNo,
            'type' => strtoupper($request["order_type"]),
            'subtotal' => $request['subtotal'],
            'shipping_cost' => $request['shipping_cost'],
            'subcharge' => $request['subcharge'],
            'total' => $total,
            'reference' => $reference,
            'discount_code' => isset($request['discount_code']) ? $request['discount_code'] : NULL
        ]);

        $this->saveOrderDetails($order, $request->all());

        //OrderPlaced::dispatch($order);
        return CustomResponse::success("Payment Link:", $order->fresh());
    }

    public function saveOrderDetails(Order $order, $data)
    {
        $orderType = strtoupper($data['order_type']);
        if($orderType === 'OCCASION SERVICE'):
            OccasionServiceDetail::create([
                'order_id' => $order->id,
                'occasion_type' => $data["details"]['occasion_type'],
                'expected_guests' => (int) $data["details"]['expected_guests'],
                'date' => $data["details"]['date'],
                'period' => $data["details"]['period'],
                'firstname' => $data["details"]['firstname'],
                'lastname' => $data["details"]['lastname'],
                'phone' => $data["details"]['phone'],
                'address' => $data["details"]['address'],
                'note' => $data["details"]['note'],
                'budget' => $data["details"]['budget']
            ]);
        elseif($orderType === 'DELIVERY SERVICE'):
            foreach($data['cart'] as $item):
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
            DeliveryServiceDetail::create([
                'order_id' => $order->id,
                'date' => $data["details"]['date'],
                'period' => $data["details"]['period'],
                'firstname' => $data["details"]['firstname'],
                'lastname' => $data["details"]['lastname'],
                'phone' => $data["details"]['phone'],
                'address' => $data["details"]['address'],
                'note' => $data["details"]['note'],
            ]);
        elseif($orderType === 'HOME SERVICE'):
            foreach($request['cart'] as $item):
                $contents = $order->contents()->create([
                    'product_id' => $data['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            endforeach;
            $detail = $order->detail()->create([
                'date' => $data['date'],
                'period' => $data['period'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'note' => $data['note'],
                'gas_filled' => $data['gas_filled'],
                'burners' => $data['burners']
            ]);
        endif;
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

    public function acceptOrder($orderId, $action)
    {
        $order = Order::find($orderId);
        if($action == 'accept'):
            $status = 'confirmed';
            elseif($action == 'decline'):
                $status = 'declined';
        endif;
        $order->update([
            'order_status' => $status
        ]);
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

        $message = "The order rescheduled date has been sent to the customer";
        return CustomResponse::success($message, $order->fresh());
    }
}