<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Services\BundleService;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    protected $bundleService;

    public function __construct(BundleService $bundleService)
    {
        $this->bundleService = $bundleService;
    }

    public function index()
    {
        return response()->json($this->bundleService->getAllBundles());
    }

    public function show($id)
    {
        $bundleResult = $this->bundleService->getBundleById($id);
        return response()
        ->json([
            'data' => $bundleResult,
            'status' => 200
        ]);
        // ->file($bundleResult->image->url);
    }

    public function store(Request $request)
    {   
        $bundleData = $request->except('course_id','thumbnail');
        $thumbnail = $request->file('thumbnail');
        $courseIds = json_decode($request->course_id);
        try {
            $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
                'thumbnail' => 'required|mimes:jpg,jpeg,png,svg|max:5120',
                'status' => 'required|in:Active,Inactive',
            ]);
            
            $bundleResult = $this->bundleService->createBundle($bundleData,$thumbnail,$courseIds);
            
            return response()->json([
                'data' => $bundleResult,
                'status' => 200,
            ]);
            //code...
        } catch (\Exception $e) {
            //throw $th;
            return response()->json([
                'data' => $e->getMessage(),
                'status'=> 500,
                ]);
        }
    }

    public function update(Request $request,$id)
    {
        $bundleData = $request->except('course_id','thumbnail');
        $thumbnail = $request->file('thumbnail');
        $courseIds = json_decode($request->course_id);
        try {
            //code...
            $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
                'thumbnail' => 'required|mimes:jpg,jpeg,png,svg|max:5120',
                'status' => 'required|in:Active,Inactive',
            ]);
            return response()->json([
                'data' => $this->bundleService->updateBundle($id, $bundleData,$thumbnail,$courseIds),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            //throw $th;
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }

    }

    public function destroy($id)
    {
        return response()->json($this->bundleService->deleteBundle($id));
    }
}
