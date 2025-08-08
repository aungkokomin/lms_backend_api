<?php

namespace App\Services;

use App\Interfaces\CourseRepositoryInterface;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\Module;
use App\Models\Referral;
use App\Models\Role;
use App\Models\User;
use App\Repositories\UserRespository;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

use function App\Helpers\referrerValidCheck;
use function App\Helpers\storeFile;
use function App\Helpers\userExistOrNot;

class UserService
{
    /**
     * @var $userRepository
     */
    protected $userRepository;
    protected $courseRepositoryInterface;

    /**
     * Summary of __construct
     * @param \App\Repositories\UserRespository $userRepository
     * @param \App\Interfaces\CourseRepositoryInterface $courseRepositoryInterface
     */
    public function __construct(UserRespository $userRepository, CourseRepositoryInterface $courseRepositoryInterface)
    {
        $this->userRepository = $userRepository;
        $this->courseRepositoryInterface = $courseRepositoryInterface;
    }

    /**
     * Summary of getAll
     * @return User
     */
    public function getAll(){
        return $this->userRepository->getAll();
    }

    /**
     * Summary of getAllWithPaginate
     * @param mixed $role
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithPaginate(){
        return $this->userRepository->getAllWithPaginate();
    }

    /**
     * Summary of getById
     * @param mixed $id
     * @return mixed
     */
    public function getById($id){
        return $this->userRepository->getById($id);
    }
    
    public function getByRole($role) {
        return $this->userRepository->getByRole($role);
    }

    /**
     * Summary of createUserBySelf
     * @param mixed $data
     * @throws \InvalidArgumentException
     * @return User
     */
    public function createUserBySelf($data){
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|string|confirmed',
            // 'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }
        
        if(!isset($data['referrer_id']) || !$data['referrer_id']){
            $referrer_id = User::role('admin')->first()->referral_id;
        }else {
            if(!referrerValidCheck($data['referrer_id'])){
                throw new InvalidArgumentException('Invalid Referrer ID Provided');
            }
            $referrer_id = $data['referrer_id'];
        }

        $result = $this->userRepository->save($data);
        
        if($result){
            $referral = new Referral();
            $referral->user_id = $result->id;
            $referral->referrer_id = $referrer_id;
            $referral->save();
        }

        return $result;
    }
    
    /**
     * Summary of updateUser
     * @param mixed $data
     * @param mixed $id
     * @throws \InvalidArgumentException
     * @return User
     */
    public function updateUser(int $id ,array $data){
        $validator = Validator::make($data, [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users',
            'password' => 'sometimes|string|confirmed',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'password' => 'string|min:8|confirmed',
        ]);

        if ($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }
        $result = $this->userRepository->update($data, $id);

        if(isset($data['profile_image'])){
            $paths = storeFile($data['profile_image'],'profile');
            if($result->image()->exists()){
                $result->image()->update([
                    'url' => $paths['url']
                ]);
            }else{
                $result->image()->create([
                    'url' => $paths['url']
                ]);
            }
        }
        $updatedUser = $this->userRepository->getById($id);
        $updatedUser->image = $result->image()->first();
        return $updatedUser;
    }

    public function updatePassword($id, $data) {

        $result = $this->userRepository->update($data, $id);
        return $result;
    }

    /**
     * Summary of createUserByOther
     * @param mixed $data
     * @throws \InvalidArgumentException
     * @return User|null
     */
    public function createUserByOther($data){
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'NRIC_number' => 'required|string|max:255|unique:users,NRIC_number,NULL,id,deleted_at,NULL',
            'nationality' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string|max:255',
            'zip_code' => 'string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'phone_number' => 'required|string|max:20|unique:users,phone_number,NULL,id,deleted_at,NULL',
            'city' => 'required|string|max:255',
            'gender' => 'required|boolean',
            'referUserId' => 'string|max:255',
            // 'password' => 'required|string|min:8|confirmed',
            'password' => 'required|string'
        ]);

        if ($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }

        if(!isset($data['referUserId']) || !$data['referUserId']){
            $referUserId = User::role('admin')->first()->referral_id;
        }else {
            if(!referrerValidCheck($data['referUserId'])){
                throw new InvalidArgumentException('Invalid Referrer ID Provided');
            }
            $referUserId = $data['referUserId'];
        }

        $result = $this->userRepository->save($data);
        
        if($result){
            $referral = new Referral();
            $referral->user_id = $result->id;
            $referral->referrer_id = $referUserId;
            $referral->save();
        }

        return $result;
    }

    /**
     * Summary of deleteUser
     * @param mixed $id
     * @return User|User[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function deleteUser($id){
        $result = $this->userRepository->delete($id);

        return $result;
    }

    public function assignUserRole($data,$id){
        $validator = Validator::make($data, [
            'role' => ['required','string','exists:roles,name']
        ]);
        if ($validator->fails()){
            throw new InvalidArgumentException($validator->errors()->first());
        }
        $user = $this->userRepository->getById($id);
        $user->assignRole($data['role']);
        return $user->fresh();
    }

    public function confirmedUserInfoUpdate($data){
        $user = Auth::user();

        $result['user'] = $userUpdated = $this->userRepository->update($data, $user->id);

        if(isset($result['user'])){

            $userUpdated->assignRole($data['role']);
            
            if($data['role'] == 'student'){

                $course_data = [
                    'user_id' => $user->id,
                    'course_id' => $data['course_id'],
                    'completed_module' => '0',
                    'total_module' => Module::where('course_id',$data['course_id'])->count(),
                    'progress_percentage' => ($result['completeModule'] / $result['totalModule']) * 100
                ];
                $result['course_progress'] = $this->courseRepositoryInterface->saveCourseProgress($course_data);
            }

        }
        
        return $result;
    }

    public function getPurchasedItems(){
        $items = $this->userRepository->getItems();
        $course = [];
        $module = [];
        $bundle = [];
        foreach($items as $item){
            if($item->itemable_type == 'course'){
                $course[] = Course::with('image')->find($item->itemable_id);
            }else if($item->itemable_type == 'module'){
                $module[] = Module::with('image')->find($item->itemable_id);
            }else if($item->itemable_type == 'bundle'){
                $bundle[] = Bundle::with('image')->find($item->itemable_id);
            }
        }
        return [
            'course' => $course ?? null,
            'module' => $module ?? null,
            'bundle' => $bundle ?? null,
        ];
    }

    public function getUserListByRole($role){
        return $this->userRepository->getByRole($role);
    }

}