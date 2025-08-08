<?php

namespace App\Http\Controllers\api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class RoleController extends Controller
{
    //
    /**
     * Summary of user
     * @var 
     */
    protected $roleservice;

    /**
     * Summary of __construct
     * @param \App\Models\User $user
     */
    public function __construct(RoleService $roleservice){
        $this->roleservice = $roleservice;
    }
    
    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(){

        $result = ['status' => 200];
        
        try{
            $result['data'] = $this->roleservice->listRole();
        } catch(\Exception $e){
            $result['status'] = 400;
            $result['error'] = $e->getMessage();
        }

        return response()->json($result);
    }

    /**
     * Summary of show
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $data = $request->all();

        $result = ['status'=> 200];
        try{
            $result['data'] = $this->roleservice->getRole($data);
        } catch(\Exception $e){
            $result['status'] = 400;
            $result['error'] = $e->getMessage();
        }
        return response()->json($result);
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $result = ['status' => 200];
        
        $data = $request->all();
        try{
            $result['data'] = $this->roleservice->createRole($data);
        }catch(\Exception $e){
            $result['status'] = 400;
            $result['message'] = $e->getMessage();
        }
        return response()->json($result);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        $result = ['status' => 200];
        $data = $request->all();
        try{
            $result['data'] = $this->roleservice->updateRole($id,$data);
        }catch(\Exception $e){
            $result['status'] = 400;
            $result['message'] = $e->getMessage();
        }
        return response()->json($result);
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        
        try {
            $result = ['status'=> 200];
            $result['data'] = $this->roleservice->deleteRole($id);
        }catch(\Exception $e){
            $result['status'] = 400;
            $result['message'] = $e->getMessage();
        }

        return response()->json($result);
    }


}
