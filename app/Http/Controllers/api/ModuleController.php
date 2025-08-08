<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ModuleService;

class ModuleController extends Controller
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'searchValue' => 'sometimes|string|nullable',
        ]);
        $searchValue = $data['searchValue'] ?? null;
        $modules = $this->moduleService->getAllModules($searchValue);
        return response()->json([
            'data' => $modules,
            'status' => 200
        ],200);
    }

    /**
     * Display a listing of the resource. Same with index but not restricted to Authenticated users
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $modules = $this->moduleService->getAllModules();
        return response()->json([
            'data' => $modules,
            'status' => 200
        ],200);
    }

    public function show($id)
    {
        try{
            $module = $this->moduleService->getModuleById($id);
            return response()->json([
                'data' => $module,
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function getByCourse(Request $request)
    {
        $courseId = $request->course_id;
        $module = $this->moduleService->getByCourse($courseId);
        return response()->json([
            'data' => $module,
            'status' => 200
        ],200);
    }

    public function store(Request $request)
    {
        $module = $this->moduleService->createModule($request->all());
        return response()->json([
            'data' => $module,
            'status' => 201
        ],201);
    }

    public function update(Request $request, $id)
    {
        try{
            $module = $this->moduleService->updateModule($id, $request->all());
            return response()->json([
                'data' => $module,
                'status' => 200
            ]);
        }catch(\Exception $e){
            return response()->json(['data' => $e->getMessage(),'status' => 400],400);
        }
    }

    public function destroy($id)
    {
        return response()->json([
            'data' => $this->moduleService->deleteModule($id),
            'status' => 200
        ], 200);
    }

    public function showModuleAtLandingPage()
    {
        $courseId = config('services.global_data.course_id');
        $module = $this->moduleService->getByShowCaseCourse($courseId);
        return response()->json([
            'data' => $module,
            'status' => 200
        ],200);
    }
}
