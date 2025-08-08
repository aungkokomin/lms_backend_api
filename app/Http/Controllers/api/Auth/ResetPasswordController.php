<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{

    public function __construct() {
        $this->middleware('role:admin')->only('resetAdminPw');
    }
    public function resetPassword(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            "token" => "required",
            "email" => "required|email|max:255",
            "password"=> "required|min:8|confirmed",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 400,
                "error" => $validator->errors()->first(),
            ],400);
        }

        $user = User::where("email", $data["email"])->first();

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();
            }
        );
        return $status === Password::PASSWORD_RESET
            ? response()->json(['status' => 200,'message' => "Password has been reset successfully!"], 200)
            : response()->json(['status' => 400,'message' => "Password reset link is expired!"], 400);
    }

    public function resetAdminPw(Request $request)
    {
        try{
            $data = $request->validate([
                'password' => 'required|min:8|confirmed'
            ]);
            $admin = User::whereHas('roles', function($q){
                $q->where('name', 'admin');
            })->first();
    
            if ($admin) {
                $admin->password = bcrypt($data['password']);
                $admin->save();
            }
    
            return response()->json(['status' => 200,'message' => "Password has been reset successfully!"], 200);
        }catch(\Exception $e){
            return response()->json(['status' => 400,'message' => $e->getMessage()], 400);
        }
    }
}
