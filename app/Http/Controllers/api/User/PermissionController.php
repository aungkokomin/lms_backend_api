<?php

namespace App\Http\Controllers\api\User;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Dflydev\DotAccessData\Exception\DataException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $permissions = Permission::all();
            return response()->json([
                'data' => $permissions,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data'=> $e->getMessage(),
                'status'=> 500
            ]);
        }
    }

    public function assignToRole(Request $request)
    {
        try {
            $data = $request->all();

            $validator = Validator::make($data, [
                'role_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new DataException($validator->errors()->first());
            }

            $permissions = array();
            $permissions = json_decode($data['permissions'],true);
            $role = Role::findByName($data['role_name']);
            $role->syncPermissions($permissions);
            return response()->json([
                'status' => 200,
                'data'=> $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data'=> $e->getMessage(),
                'status'=> 500
            ]);
        }
    }

    public function assignToUser(Request $request)
    {
        try {
            $data = $request->all();
            
            $user_id = $data['user_id'];
            $permissions = json_decode($data['permissions'],true);
            $user = User::find($user_id);
            $user->syncPermissions($permissions);
            return response()->json([
                'status'=> 200,
                'data'=> $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data'=> $e->getMessage(),
                'status'=> 500
            ]);
        }
    }
}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    