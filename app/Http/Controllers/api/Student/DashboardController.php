<?php

namespace App\Http\Controllers\api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserItemResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function currentProgram()
    {
        try {
            $user = User::findOrFail(Auth::user()->id);
            if($user->userItem()->count() == 0){
                return response()->json([
                    'data' => [],
                    'status' => 404
                ], 404);
            }else{
                $item = $user->userItem()->orderBy('created_at', 'desc')->get();
                return response()->json([
                    'data' => UserItemResource::collection($item),
                    'status' => 200
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch items',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
