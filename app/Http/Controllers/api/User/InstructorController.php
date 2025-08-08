<?php

namespace App\Http\Controllers\api\User;

use App\Http\Controllers\Controller;
use App\Services\InstructorService;
use App\Services\UserService;
use Illuminate\Http\Request;

class InstructorController extends Controller
{

    protected $userService;
    protected $instructorService;
    protected $role;

    public function __construct(InstructorService $instructorService,UserService $userService) {
        $this->instructorService = $instructorService;
        $this->userService = $userService;
        $this->role = 'instructor';
    }

    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        try {
            return response()->json([
                'data' => $this->userService->getAllWithPaginate(),
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{
            $result = ['status' => 200];
        
            $data = $request->validate([
                'email' => 'required|email|exists:users',
                'qualification' => 'required|string|max:100',
                'experienced_years' => 'required|integer|min:1',
                'specialization' => 'required|string|max:255',
                'bio' => 'required|string',
                'certifications' => 'required|string',
                'hourly_rate' => 'required|numeric',
                'availability_schedule' => 'required|string|max:255',
                'rating' => 'required|numeric',
                'social_links' => 'required|string|max:255',
            ]);

            $data['role'] = $this->role;


            $result['data'] = $this->instructorService->createInstructor($data);
            
            return response()->json($result);
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $result['status'] = 200;
            $result['data'] = $this->instructorService->showInstructor($id);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ]);
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
}
