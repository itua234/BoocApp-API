<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function order(Request $request)
    {
        return $this->orderService->order($request);
    }

    public function rescheduleOrder(Request $request, $orderId)
    {
        return $this->orderService->rescheduleOrder($request, $orderId);
    }

    public function quoteNewPrice(Request $request, $orderId)
    {
        return $this->orderService->quoteNewPrice($request, $orderId);
    }
}
