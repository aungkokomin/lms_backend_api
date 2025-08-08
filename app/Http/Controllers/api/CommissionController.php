<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommissionResource;
use App\Models\Commission;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    protected $commissionService;

    /**
     * CommissionController constructor.
     * @param CommissionService $commissionService
     */
    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try{
            $commissions = $this->commissionService->list();
            $result = [
                'data' => CommissionResource::collection($commissions),
                'current_page' => $commissions->currentPage(),
                'last_page' => $commissions->lastPage(),
                'total' => $commissions->total(),
                'per_page' => $commissions->perPage(),
                'status' => 200
            ];
        }catch(\Exception $e){
            $result['status'] = 400;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result,$result['status']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Commission $commission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Commission $commission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commission $commission)
    {
        //
    }
    
    /**
     * Summary of dashboardStatus
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function dashboardStatus(){
        try{
            $result['status'] = 200;
            $result['data'] = $this->commissionService->calculateStats();
        }catch(\Exception $e){
            $result['status'] = 400;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result,$result['status']);
    }
}
