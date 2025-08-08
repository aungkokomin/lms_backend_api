<?php

namespace App\Services;

use App\Repositories\CommissionSettingRepository;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Validator;

class CommissionSettingService
{
    // Add your repository methods here
    /**
     * Summary of comsettingrepository
     * @var 
     */
    protected $comsettingrepository;
    
    /**
     * Summary of __construct
     * @param \App\Repositories\CommissionSettingRepository $comsettingrepository
     */
    public function __construct(CommissionSettingRepository $comsettingrepository)
    {
        $this->comsettingrepository = $comsettingrepository;
    }

    /**
     * Summary of getCommissionSettings
     * @return \App\Models\CommissionSetting[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCommissionSettings()
    {
        return $this->comsettingrepository->get();
    }

    /**
     * Summary of getCommissionSettingPaginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCommissionSettingPaginate(){
        return $this->comsettingrepository->getWithPaginate();
    }

    /**
     * Summary of saveCommissionSettings
     * @param mixed $data
     * @throws \Exception
     * @return \App\Models\CommissionSetting|null
     */
    public function saveCommissionSettings($data){
        $validator = Validator::make($data,[
            'name' => 'required|string|max:255',
            'commission_rate' => 'required|string|max:255',
            'description' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $result = $this->comsettingrepository->save($data);

        return $result;
    }

    /**
     * Summary of getCommissionSettingById
     * @param mixed $id
     * @return \App\Models\CommissionSetting|\App\Models\CommissionSetting[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getCommissionSettingById($id){
        return $this->comsettingrepository->getById($id);
    }

    /**
     * Summary of updateCommissionSettingById
     * @param mixed $id
     * @param mixed $data
     * @throws \Exception
     * @return \App\Models\CommissionSetting|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function updateCommissionSetting($id,$data){
        $validator = Validator::make($data,[
            'name'=> 'string|max:255',
            'commission_rate' => 'string|max:255',
            'description'=> 'string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $result = $this->comsettingrepository->update($id,$data);
        return $result;
    }

    /**
     * Summary of deleteCommissionSetting
     * @param mixed $id
     * @return bool
     */
    public function deleteCommissionSetting($id){
        $result = $this->comsettingrepository->delete($id);
        return $result;
    }
}