<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class StudentFilterExport implements FromCollection, WithHeadings
{

    protected $searchValue;
    protected $start_date;
    protected $end_date;

    public function __construct($searchValue = NULL,$start_date = NULL,$end_date = NULL) {
        $this->searchValue = $searchValue;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }


    public function collection()
    {
        $searchValue = $this->searchValue;
        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $selectedColumns = [
            'full_name',
            'u.email',
            'identification_number',
            'students.NRIC_number',
            'students.phone_number',
            'students.city',
            'students.address',
            'students.zip_code',
            'students.nationality',
            'students.date_of_birth'
        ];
        $student = Student::leftJoin('users as u','students.user_id','=','u.id')
        ->when($searchValue,function($query) use ($searchValue){
            $query->where('students.identification_number','like','%'.$searchValue.'%')
            ->orWhere('students.full_name','like','%'.$searchValue.'%')
            ->orWhere('students.NRIC_number','like','%'.$searchValue.'%')
            ->orWhere('u.email','like','%'.$searchValue.'%');
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('students.created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('students.created_at','<=',$end_date);
        })
        ->select($selectedColumns)
        ->get();
        Log::info('student : '.$student);
        return $student;
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Email',
            'Identification Number',
            'NRIC Number',
            'Phone Number',
            'City',
            'Address',
            'Zip Code',
            'Nationality',
            'Date of Birth'
        ];
    }
}
