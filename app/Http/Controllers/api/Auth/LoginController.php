<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserLoginResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        // Attempt to authenticate the user
        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Email or Password is incorrect!'], 401);
        }

        $user = User::with('image')->findOrFail(Auth::user()->id);

        if(!$user->hasVerifiedEmail()){
            return response()->json([
                'message' => 'Please verify your email first',
                'status' => 401
            ],401);
        }

        // Generate a personal access token
        if($user->tokens()->exists()){
            $user->tokens()->delete();
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => UserLoginResource::make($user),
            'roles' => $user->roles->pluck('name'),
            'message' => 'Login successful'
        ], 200);
    }

    /**
     * Handle logout and revoke the token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
