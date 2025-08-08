<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\UserGrantCode;
use App\Services\GrantCodeService;
use Illuminate\Http\Request;

class GrantCodeController extends Controller
{
    protected $grantCodeService;

    /**
     * GrantCodeController constructor.
     * @param GrantCodeService $grantCodeService
     */
    public function __construct(GrantCodeService $grantCodeService)
    {
        $this->grantCodeService = $grantCodeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(string $id)
    {
        //
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

    public function showGrantInfo(Request $request){
        try{
            $data = $request->validate([
                'grant_code' => 'required|string'
            ]);

            $result = $this->grantCodeService->getGrantInfo($data);

            return response()->json([
                'data' => $result['data'],
                'message' => $result['message'],
                'status' => 200,
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }
}
