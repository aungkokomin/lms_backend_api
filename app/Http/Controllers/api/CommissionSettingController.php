<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\CommissionSettingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommissionSettingController extends Controller
{
    protected $comsettingservices;

    /**
     * 
     */
    public function __construct(CommissionSettingService $comsettingservices)
    {
        $this->comsettingservices = $comsettingservices;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $result = ['status' => 200];
            $result['data'] = $this->comsettingservices->getCommissionSettings();
        } catch (\Exception $e) {
            $result = [
                'status'=> 500,
                'message'=> $e->getMessage()
            ];
        }
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $result = ['status' => 200];
        $data = $request->all();

        try {
            $result['data'] = $this->comsettingservices->saveCommissionSettings($data);
        } catch (\Exception $e) {
            $result['status'] = 400;
            $result['message'] = $e->getMessage();
        }
        
        return response()->json($result,$result['status']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $result = ['status' => 200];
        try {
            //code...
            $result['data'] = $this->comsettingservices->getCommissionSettingById($id);
        } catch (\Exception $e) {
            //throw $th;
            $result['status'] = 500;
            $result['message'] = $e->getMessage();
        }
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $data = $request->all();
        $result = ['status'=> 200];
        try {
            $result['data'] = $this->comsettingservices->updateCommissionSetting($id, $data);
        }catch (\Exception $e) {
            $result['status'] = 500;
            $result['message'] = $e->getMessage();
        }
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $result = ['status'=> 200];
        try {
            $this->comsettingservices->deleteCommissionSetting($id);
        } catch (Exception $e){
            $result['status'] = 500;
            $result['message'] = $e->getMessage();
        }
        return response()->json($result);
    }
}
