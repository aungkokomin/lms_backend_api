<?php

namespace App\Repositories;

use App\Models\CommissionSetting;
use Dflydev\DotAccessData\Exception\DataException;

class CommissionSettingRepository
{
    // Add your repository methods here
    protected $commission_setting;

    /**
     * Summary of __construct
     * @param \App\Models\CommissionSetting $commission_setting
     */
    public function __construct(CommissionSetting $commission_setting)
    {
        $this->commission_setting = $commission_setting;
    }

    /**
     * Summary of getCommissionSettings
     * @return CommissionSetting[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        return $this->commission_setting->get();
    }

    /**
     * Summary of getCommissionSettingById
     * @param mixed $id
     * @return CommissionSetting|CommissionSetting[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getById($id)
    {
        return $this->commission_setting->findOrFail($id);
    }

    /**
     * Summary of getWithPaginate
     * @param \App\Models\CommissionSetting $commission_setting
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getWithPaginate()
    {
        return $this->commission_setting->paginate(10);
    }

    /**
     * Summary of createCommissionSetting
     * @param mixed $data
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return CommissionSetting|null
     */
    public function save($data)
    {
        $commission_setting = new $this->commission_setting;
        try {
            $commission_setting->fill($data);
            $commission_setting->save();
            return $commission_setting->fresh();
        } catch (\Exception $e) {
            throw new DataException($e->getMessage());
        }
    }

    /**
     * Summary of updateCommissionSetting
     * @param mixed $id
     * @param mixed $data
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return CommissionSetting|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function update($id, $data)
    {
        $commission_setting = $this->getById($id);
        try {
            $commission_setting->fill($data);
            $commission_setting->save();
            return $commission_setting->fresh();
        } catch (\Exception $e) {
            throw new DataException($e->getMessage());
        }
    }

    /**
     * Summary of delete
     * @param mixed $id
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return bool
     */
    public function delete($id)
    {
        $commission_setting = $this->getById($id);
        try {
            $commission_setting->delete();
            return true;
        } catch (\Exception $e) {
            throw new DataException($e->getMessage());
        }
    }
}