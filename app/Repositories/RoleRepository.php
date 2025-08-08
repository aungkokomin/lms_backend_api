<?php

namespace App\Repositories;

use App\Models\Role;
use Dflydev\DotAccessData\Exception\DataException;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as SpatieRoleModel;


class RoleRepository
{
    protected $role;
    // Add your repository methods here
    /**
     * Summary of __construct
     * @param \Spatie\Permission\Models\Role $role
     */
    public function __construct(Role $role){
        $this->role = $role;
    }

    /**
     * Summary of all
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function all(){
        return $this->role->paginate(10);
    }

    /**
     * Summary of getByName
     * @param string $name
     * @return SpatieRoleModel[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getByName(string $name){
        return $this->role->where('name','LIKE',"%$name%")->get();
    }

    /**
     * Summary of create
     * @param array $data
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return Role|\Spatie\Permission\Contracts\Role
     */
    public function create(array $data){
        try{
            $role = SpatieRoleModel::create([
                'name'=> $data['name'],
            ]);
            return $role;
        }catch(\Exception $e){
            throw new DataException($e->getMessage());
        }
    }

    /**
     * Summary of update
     * @param array $data
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return bool
     */
    public function update($id, array $data){
        $role = $this->role->find($id);
        try{
            return $role->update([
                'name'=> $data['name'],
            ]);

        }catch(\Exception $e){
            throw new DataException($e->getMessage());
        }
    }

    /**
     * Summary of delete
     * @param mixed $id
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return Role|Role[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function delete($id){
        try{
            $role = $this->role->findOrFail($id);
            return $role->delete();
        }catch(\Exception $e){
            throw new DataException($e->getMessage());
        }
    }
}