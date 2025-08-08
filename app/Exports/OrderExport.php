<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection , WithHeadings
{

    protected $searchValue;
    protected $start_date;
    protected $end_date;
    public function __construct($searchValue = NULL,$start_date = NULL,$end_date = NULL) {
        $this->searchValue = $searchValue;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $searchValue = $this->searchValue;
        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $selectedColumns = [
            'o.order_uid as Order ID',
            'o.transaction_id as Transaction ID',
            'u.name as Username',
            'o.order_price as Order Price',
            'p.amount as Payment Amount',
            'p.currency as Currency',
            'o.order_type as Order Type',
            'o.status as Order Status',
            'p.status as Payment Status',
            'o.order_date as Order Date',
            'p.payment_date as Payment Date',
            'o.completed_at as Order Complete Date',
        ];

        $results = DB::table('orders as o')
        ->leftJoin('users as u','o.user_id','=','u.id')
        ->leftJoin('payments as p','o.transaction_id','=','p.transaction_id')
        ->when($searchValue,function($query) use ($searchValue){
            $query->where('o.order_id','like','%'.$searchValue.'%');
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('o.created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('o.created_at','<=',$end_date);
        })
        ->select($selectedColumns)
        ->get();

        return $results;   
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Transaction ID',
            'Username',
            'Order Price',
            'Payment Amount',
            'Currency',
            'Order Type',
            'Order Status',
            'Payment Status',
            'Order Date',
            'Payment Date',
            'Order Complete Date',
        ];
    }
}
