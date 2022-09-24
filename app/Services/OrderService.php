<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Util\{CustomResponse, Paystack, Flutterwave, Helper};
//use App\Http\Resources\UserResource;
use App\Http\Requests\{LoginRequest};
use Illuminate\Support\Facades\{DB, Http};
use App\Models\{User, Dish, DishExtra, DishCategory, Order};


class OrderService
{  
    public function order(Request $request)
    {
        $user = auth()->user();
        $total = $request['total'];
        $reference = Helper::generateReference($user->id);
        $channel = strtoupper($request['payment_channel']);
        $paymentUrl = $this->generatePaymentUrl($user, $channel, $total, $reference);
        
        $order = Order::create([
            'user_id' => $user->id,
            'chef_id' => $request['chef_id'],
            'total' => $total,
            'reference' => $reference,
            'payment_channel' => $channel,
            'discount_code' => isset($request['discount_code']) ? $request['discount_code'] : NULL
        ]);

        return $paymentUrl;
    }

    public function saveOrderDetails(Order $order, Request $data)
    {
        if($data['order_type'] === 'OCCASION SERVICE'):
            $details = $order->detail()->create([
                'occasion_type' => $data['occasion_type'],
                'expected_guests' => (int) $data['expected_guests'],
                'date' => $data['date'],
                'period' => $data['period'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'note' => $data['note'],
                'budget' => $data['budget']
            ]);
        elseif($data['order_type'] === 'DELIVERY SERVICE'):
            foreach($request['cart'] as $item):
                $contents = $order->contents()->create([
                    'dish_id' => $data['dish_id'],
                    'dish_quantity' => $data['dish_quantity'],
                    'dish_price' => $data['dish_price'],
                    'extra_id' => $data['extra_id'],
                    'extra_quantity' => $data['extra_quantity'],
                    'extra_price' => $data['extra_price']
                ]);
            endforeach; 
            $detail = $order->detail()->create([
                'date' => $data['date'],
                'period' => $data['period'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'note' => $data['note']
            ]);
        elseif($data['order_type'] === 'HOME SERVICE'):
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

    public function acceptOrDeclineOrder()
    {
        
    }
}