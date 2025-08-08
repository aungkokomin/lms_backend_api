<?php

namespace App\Repositories;

use App\Http\Resources\OrderResource;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    // Add your repository methods here
    protected $order;

    public function __construct(Order $order) {
        $this->order = $order;
    }

    public function list($searchValue = NULL,$start_date = NULL,$end_date = NULL){
        return $this->order->with('payment','payment.image','user')
        ->when($searchValue,function($query) use ($searchValue){
            $query->where('order_uid',$searchValue);
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('created_at','<=',$end_date);
        })
        ->orderBy('created_at','desc')
        ->paginate(10);
    }

    public function get($id) 
    {
        $order = $this->order->with('payment')->find($id);
        return new OrderResource($order);
    }

    public function listByUser($user_id)
    {
        $orders = $this->order->where('user_id',$user_id)->with('payment','payment.image')->orderBy("created_at","desc")->paginate(10);
        return $orders;
    }

    public function create($data) 
    {
        $order = $this->order->create($data);
        return $order->fresh();
    }

    public function update($id,array $data)
    {
        
    }

    public function delete($id)
    {
        
    }
}