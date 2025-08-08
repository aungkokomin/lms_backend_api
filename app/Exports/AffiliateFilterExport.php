<?php

namespace App\Exports;

use App\Models\Affiliator;
use App\Models\Role;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AffiliateFilterExport implements FromCollection, WithHeadings
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
        $user = new User();
        $selectedColumns = [
            'id',
            'name',
            'email',
            'referral_id',
            'email_verified_at',
            'created_at',
        ];

        $results = $user->when($searchValue,function($query) use ($searchValue){
            $query->where('users.name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('users.created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('users.created_at','<=',$end_date);
        })
        ->whereHas('roles',function($query){
            $query->where('roles.name',Role::ROLE_STUDENT)->orWhere('name',Role::ROLE_GUEST);
        })
        // ->orderByDesc('id')
        ->select($selectedColumns)
        ->get();

        return $results;
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Email',
            'Own Referral Code',
            'Verified At',
            'Registered At',
        ];
    }
}
