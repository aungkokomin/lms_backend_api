<?php

namespace App\Http\Controllers\api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserItemResource;
use App\Http\Resources\UserLoginResource;
use App\Http\Resources\UserResource;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Str;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $result = $this->userService->getAllWithPaginate();
        return response()->json([
            'data' => $result,
            'status' => 200
        ]);
    }

    /**
     * Summary of registerUser
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function registerUser(Request $request)
    {
        $result = ['status' => 200];
        $data = $request->all();
        try{
            $result['data'] = $this->userService->createUserBySelf($data);
            return response()->json($result);
        }catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'data' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->all();
        $result = ['status' => 200];
        try {
            $result['data'] = $this->userService->createUserByOther($data);
        } catch (\Exception $e) {
            $result = [
                'status' => 400,
                'error' => $e->getMessage()
            ];
        }
        
        return response()->json($result, $result['status']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $result = ['status' => 200];
        try {
            $user = $this->userService->getById($id);
            $result['data'] = new UserResource($user);
        } catch (\Exception $e) {
            $result = [
                'status'=> 404,
                'error'=> $e->getMessage()
            ];
        }

        return response()->json($result, $result['status']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id,Request $request)
    {
        //
        $result = ['status' => 200];
        try {
            $user = $this->userService->updateUser($id, $request->all());
            $result['data'] = new UserLoginResource($user);
        } catch (\Exception $e) {
            $result = [
                'status'=> 400,
                'error'=> $e->getMessage()
            ];
        }

        return response()->json($result, $result['status']);
    }

    public function changePassword(Request $request, string $id)
    {
        $result = ['status'=> 200];
        try {
            $data = $request->validate([
                'password'=> 'required|string|min:8|confirmed|different:old_password',
                'old_password'=> 'required|string|min:8'
            ]);

            $result['data'] = $this->userService->updatePassword($id, $data);
        } catch (\Exception $e) {
            $result = [
                'status'=> 500,
                'error'=> $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $result = ['status' => 200];
        try{
            $result['data'] = $this->userService->deleteUser($id);
        } catch (\Exception $e) {
            $result = [
                'status'=> 500,
                'error'=> $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
    }

    /**
     * Summary of roleAssign
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function roleAssign(Request $request, string $id){
        $result = ['status'=> 200];
        $data = $request->all();
        try{
            $result['data'] = $this->userService->assignUserRole($data,$id);
        } catch (\Exception $e) {
            $result = [
                'status'=> 500,
                'error'=> $e->getMessage()
            ];
        }

        return response()->json($result);
    }

    public function getPurchaseItems(Request $request)
    {
        try {
            $result = $this->userService->getPurchasedItems();
            
            return response()->json([
                // 'user_id' => $userId,
                'data' => $result,
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getUserListByRole(Request $request)
    {
        try {
            $data = $request->validate([
                'role' => 'required|string'
            ]);
            $result = $this->userService->getUserListByRole($data['role']);
            return response()->json([
                'data' => $result,
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'error' => $e->getMessage()
            ]);
        }
    }
}
