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

    public function makePayment(Request $request)
    {
        return $this->orderService->makePayment($request);
    }

    public function acceptOrDeclineOrder(Request $request, $orderId)
    {
        return $this->orderService->acceptOrDeclineOrder($request, $orderId);
    }

    public function rescheduleOrder(Request $request, $orderId)
    {
        return $this->orderService->rescheduleOrder($request, $orderId);
    }

    public function quoteNewPrice(Request $request, $orderId)
    {
        return $this->orderService->quoteNewPrice($request, $orderId);
    }

    public function fetchOrders($userId)
    {
        return $this->orderService->fetchOrders($userId);
    }

    public function viewOrder($orderId)
    {
        return $this->orderService->viewOrder($orderId);
    }
    

}
