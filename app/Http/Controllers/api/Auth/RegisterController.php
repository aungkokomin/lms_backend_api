<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use App\Models\User;

use Hash;

class RegisterController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * PostController Constructor
     *
     * @param UserService $userService
     *
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        
        $data = $request->all();
        $result = ['status' => 200];
        try{
            $result['data'] = $user = $this->userService->createUserBySelf($data);
            $user->assignRole(Role::ROLE_GUEST);
            $user->sendEmailVerificationNotification();
            $result['message'] = 'Email verification link sent to your email';
            
        }catch(\Exception $e){
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($result, $result['status']);
    }
}
