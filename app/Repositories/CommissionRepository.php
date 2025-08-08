<?php

namespace App\Repositories;

use App\Http\Resources\CommissionResource;
use App\Interfaces\CommissionRepositoryInterface;
use App\Models\Commission;
use App\Models\Referral;
use Dflydev\DotAccessData\Exception\DataException;

class CommissionRepository implements CommissionRepositoryInterface
{   
    // Add your repository methods here
    
    /**
     * Summary of commission
     * @var 
     */
    protected $commission;

    /**
     * Summary of __construct
     * @param \App\Models\Commission $commission
     */
    public function __construct(Commission $commission) {
        $this->commission = $commission;
    }

    /**
     * Summary of getAll
     * @return Commission[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll() 
    {
        return $this->commission->with('user','referral')->get();
    }

    /**
     * Summary of getAllWithPaginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithPaginate(){
        return $this->commission->with('user','referral')->paginate(10);
    }

    /**
     * Summary of getByUserId
     * @param mixed $user_id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByUserId($user_id)
    {
        return $this->commission->with('user','referral')->where("user_id", $user_id)->paginate(10);
    }

    /**
     * Summary of getByReferralId
     * @param mixed $referral_id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByReferralId($referral_id){
        $result = $this->commission->with('user','referral')->where('referral_id', $referral_id)->paginate(10);
        return $result;
    }

    /**
     * Summary of save
     * @param mixed $data
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return Commission|null
     */
    public function save($data)
    {
        $commission = new $this->commission;
        try {
            $commission->user_id = $data["user_id"];
            $commission->referral_id = $data['referral_id'];
            $commission->commission_amount = $data['commission_amount'];
            $commission->description = $data['description'];

            $commission->save();
            return $commission->fresh();
        } catch (\Exception $e) {
            throw new DataException($e->getMessage());
        }   
    }

    /**
     * Summary of delete
     * @param mixed $id
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return void
     */
    public function delete($id){
        $commission = Commission::find($id);
        try {
            $commission->delete();
        } catch (\Exception $e) {
            throw new DataException($e->getMessage());
        }
    }

    /**
     * Summary of deleteByUserId
     * @param mixed $user_id
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return bool|null
     */
    public function deleteByUserId($user_id){
        try {
            $commission = Commission::where('user_id', $user_id)->delete();
            return $commission;
        } catch (\Exception $e) {
            throw new DataException($e->getMessage());
        }
    }

    /**
     * Summary of deleteByReferralId
     * @param mixed $referral_id
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return bool|null
     */
    public function deleteByReferralId($referral_id){
        try {
            $commission = Commission::where('referral', $referral_id)->delete();
            return $commission;
        } catch (\Exception $e) {
            throw new DataException($e->getMessage());
        }
    }
}