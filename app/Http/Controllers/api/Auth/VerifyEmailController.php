<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{

    public function verify(Request $request)
    {

        $user = User::find($request->id);
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
                'status' => 200
            ]);
        }
        if ($user->markEmailAsVerified()) {
            return response()->json([
                'message' => 'Email verified successfully',
                'status' => 200
            ]);
        }
        return response()->json([
            'message' => 'Email verification failed',
            'status' => 400
        ]);
    }

    public function resend(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required|email'
            ]);
            $user = User::where('email', $request->email)->first();
            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'message' => 'Email already verified',
                    'status' => 200
                ]);
            }
            $user->sendEmailVerificationNotification();
            return response()->json([
                'message' => 'Email verification link sent to your email',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ]);
        }
    }
}
