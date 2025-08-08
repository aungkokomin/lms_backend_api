<?php

namespace App\Services;

use App\Exports\OrderExport;
use App\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use function App\Helpers\writeTypeIdentifier;

class OrderService
{
    // Add your repository methods here
    protected $orderRepositoryInterface;

    public function __construct(OrderRepositoryInterface $orderRepositoryInterface) {
        $this->orderRepositoryInterface = $orderRepositoryInterface;
    }

    /**
     * Summary of downloadOrderList
     * @param mixed $searchValue 
     * @param mixed $start_date 
     * @param mixed $end_date 
     * @param mixed $format 
     * @return mixed
     */
    public function downloadOrderList($searchValue = NULL,$start_date = NULL,$end_date = NULL,$format = NULL){
        $writeType = writeTypeIdentifier($format);
        $filePath = 'public/excel/orders_list.'.$format.'';
        $result = Excel::store(new OrderExport($searchValue,$start_date,$end_date), $filePath, NULL ,$writeType);
        
        if($result){
            return Storage::url($filePath);
        }else{
            throw new \Exception('Failed to download order list');
        }
    }

    public function orderList($searchValue = NULL,$start_date = NULL,$end_date = NULL){
        return $this->orderRepositoryInterface->list($searchValue,$start_date,$end_date);
    }

    public function orderDetail($id){
        return $this->orderRepositoryInterface->get($id);
    }

    public function userOrders($user_id){
        return $this->orderRepositoryInterface->listByUser($user_id);
    }

}