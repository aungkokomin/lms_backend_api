<?php

namespace App\Http\Controllers\api;

use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService) {
        $this->orderService = $orderService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $data = $request->validate([
                'searchValue' => 'sometimes|string|nullable',
                'start_date' => 'sometimes|date|nullable',
                'end_date' => 'sometimes|date|nullable',
            ]);
            $searchValue = $data['searchValue'] ?? null;
            $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
            $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
            $orders = $this->orderService->orderList($searchValue,$start_date,$end_date);
            return response()->json([
                'data' => OrderResource::collection($orders),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'total' => $orders->total(),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    
    public function downloadOrderList(Request $request)
    {
        //
        try {
            $data = $request->validate([
                'searchValue' => 'sometimes|string|nullable',
                'start_date' => 'sometimes|date|nullable',
                'end_date' => 'sometimes|date|nullable',
                'format' => 'sometimes|string|in:xlsx,csv,xls,pdf',
            ]);
            $searchValue = $data['searchValue'] ?? null;
            $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
            $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
            $format = $data['format'] ?? 'csv';
            return response()->json([
                'data' => $this->orderService->downloadOrderList($searchValue,$start_date,$end_date,$format),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            return response()->json([
                'data' => $this->orderService->orderDetail($id),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getUserOrder(string $user_id)
    {
        try {
            $orders = $this->orderService->userOrders($user_id);
            return response()->json([
                'data' => OrderResource::collection($orders),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'total' => $orders->total(),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }
}
