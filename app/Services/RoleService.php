<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Dflydev\DotAccessData\Exception\DataException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleService
{
    // Add your repository methods here
    protected $roleRepo;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }
    
    /**
     * Summary of listRole
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listRole(){
        return $this->roleRepo->all();
    }

    /**
     * Summary of getRole
     * @param string $id
     * @return \App\Models\Role|\App\Models\Role[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRole(array $data){
        $validator = Validator::make($data,[
            "name" => "string|max:255",
        ]);

        if ($validator->fails()) {
            throw new DataException($validator->errors()->first());
        }
        
        $name = $data["name"];
        return $this->roleRepo->getByName($name);
    }

    /**
     * Summary of createRole
     * @param array $data
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return \App\Models\Role|\Spatie\Permission\Contracts\Role
     */
    public function createRole(array $data){
        $validator = Validator::make($data,[
            "name" => "required|string|max:255|unique:roles",
        ]);

        if ($validator->fails()) {
            throw new DataException($validator->errors()->first());
        }

        $role = $this->roleRepo->create($data);

        return $role;
    }

    /**
     * Summary of updateRole
     * @param mixed $id
     * @param array $data
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return bool
     */
    public function updateRole($id, array $data){
        $validator = Validator::make($data,[
            "name" => ["required","string","max:255",Rule::unique("users")->ignore($id)],
        ]);

        if ($validator->fails()) {
            throw new DataException($validator->errors()->first());
        }
        
        $role = $this->roleRepo->update($id, $data);

        return $role;
    }

    /**
     * Summary of deleteRole
     * @param mixed $id
     * @return \App\Models\Role|\App\Models\Role[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function deleteRole($id){
        return $this->roleRepo->delete($id);
    }
}