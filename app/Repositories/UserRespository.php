<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserItem;
use Dflydev\DotAccessData\Exception\DataException;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Str;

class UserRespository
{
    protected $user;
    // Add your repository methods here

    /**
     * 
     * @param \App\Models\User $user
     */
    public function __construct(User $user){
        $this->user = $user;
    }

    /**
     * Get all users.
     *
     * @return User $user
     */
    public function getAll()
    {
        return $this->user->get();
    }

    /**
     * Summary of getAllWithPaginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithPaginate(){
        return $this->user->paginate(50);
    }

    /**
     * Get user by id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->user->with('image')->findOrFail($id);
    }

    public function getByRole($role)
    {
        return $this->user->role($role)->with('student','affiliator')->get();
    }

    /**
     * Summary of save
     * @param mixed $data
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return User|null
     */
    public function save($data)
    {
        if(isset($data['date_of_birth'])){
            $data['date_of_birth'] = date('Y-m-d',strtotime($data['date_of_birth']));
        }
        $user = new $this->user;
        
        try{
            // $user->NRIC_number = isset($data['NRIC_number']) ? $data['NRIC_number'] : NULL;
            // $user->nationality = isset($data['nationality']) ? $data['nationality'] : NULL;
            // $user->date_of_birth = isset($data['date_of_birth']) ? $data['date_of_birth'] : NULL;
            // $user->address = isset($data['address']) ? $data['address'] : NULL;
            // $user->phone_number = isset($data['phone_number']) ? $data['phone_number'] : NULL;
            // $user->city = isset($data['city']) ? $data['city'] : NULL;
            // $user->gender = isset($data['gender']) ? $data['gender'] : NULL;
            $user->name = isset($data['name']) ? $data['name'] : $user->name;
            $user->email = isset($data['email']) ? $data['email'] : $user->email;
            $user->referral_id = strtoupper(uniqid());
            $user->password = isset($data['password']) ? bcrypt($data['password']) : $user->password;

            $user->save();
            return $user->fresh();
        } catch (\Exception $e) {
            throw new DataException($e->getMessage());
        }

    }

    /**
     * Summary of update
     * @param mixed $data
     * @param mixed $id
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return User|User[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function update($data, $id)
    {
        try {
            $user = $this->user->findOrFail($id);

            if(isset($data['old_password'])){
                if(!password_verify($data['old_password'], $user->password)){
                    throw new InvalidArgumentException('Old Password is incorrect');
                }
            }

            $user->update([
                'name' => isset($data['name']) ? $data['name'] : $user->name,
                'email' => isset($data['email']) ? $data['email'] : $user->email,
                'password' => isset($data['password']) ? bcrypt($data['password']) : $user->password,
            ]);

            return $user->fresh();
        } catch(\Exception $e){
            throw new DataException($e->getMessage());
        }
        
    }

    /**
     * Summary of updatePassword
     * @param mixed $data
     * @param mixed $id
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return void
     */
    public function updatePassword($data,$id){
        try {
            $user = $this->user->find($id);
            $user->password = isset($data['password']) ? bcrypt($data['password']) : $user->password;
            $user->save();
        } catch (\Exception $e){
            throw new DataException($e->getMessage());
        }
    }

    /**
     * Summary of delete
     * @param mixed $id
     * @throws \InvalidArgumentException
     * @return User|User[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function delete($id)
    {
        
        try{
            return $this->user->find($id)->delete();

        }catch(\Exception $e){
            throw new InvalidArgumentException($e->getMessage());
        }

    }

    public function getItems()
    {
        $user = Auth::user();
        $items = UserItem::where('user_id',$user->id)->get();
        return $items; 
    }
}