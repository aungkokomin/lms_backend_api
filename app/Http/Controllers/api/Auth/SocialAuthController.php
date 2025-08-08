<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserLoginResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }
    /**
     * Handle the incoming request to login with Google.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    //
    public function loginWithGoogle()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        // Check if user already exists
        if($googleUser){
            $socialId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $name = $googleUser->getName();
            $token = $googleUser->token;
        }
        try{
            $socialUser = Socialite::driver('google')->userFromToken($token);
        }catch(\Exception $e){
            return response()->json(['error' => 'Invalid token or user not found'], 401);
        }

        try{
            $user = User::where('email', $socialUser->getEmail())->first();
            if (!$user) {
                // If user does not exist, create a new user
                $password = str()->random(16); // Generate a random password
                $user = $this->userService->createUserBySelf([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt($password), // Hash the password
                ]);
                $user->assignRole('guest'); // Assign a default role
                $user->sendEmailVerificationNotification(); // Send email verification notification
            }
            // If user exists, Authenticate the user
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
        }catch(\Exception $e){
            return response()->json(['error' => 'Failed to login with Google: ' . $e->getMessage()], 500);
        }
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        // Check if user already exists
        if($googleUser){
            $socialId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $name = $googleUser->getName();
            $token = $googleUser->token;
        }
        // Redirect to your API route with the necessary parameters
        return redirect()->route('api.google.login', [
            'social_id' => $socialId,
            'email' => $email,
            'name' => $name,
            'token' => $token,
        ]);
    }

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }
}
