<?php

namespace App\Repositories;

use App\Interfaces\StudentRepositoryInterface;
use App\Models\Student;
use App\Models\User;

use function App\Helpers\generateCustomStudentId;

class StudentRepository implements StudentRepositoryInterface
{
    // Add your repository methods here
    protected $student;
    protected $user;

    public function __construct(Student $student,User $user) {
        $this->student = $student;
        $this->user = $user;
    }
    public function list($searchValue = NULL,$start_date = NULL,$end_date = NULL){
        return $this->student->with('user','education','education.document')
        ->when($searchValue,function($query) use ($searchValue){
            $query->where('identification_number','like','%'.$searchValue.'%')
            ->orWhere('full_name','like','%'.$searchValue.'%')
            ->orWhere('NRIC_number','like','%'.$searchValue.'%')
            ->orWhereHas('user',function($query) use ($searchValue){
                $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
            });
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('created_at','<=',$end_date);
        })
        ->orderBy('id','desc')
        ->paginate(10);
    }

    public function downloadCSV($searchValue = NULL){
        $students = $this->student->with('user','education','education.document')
        ->when($searchValue,function($query) use ($searchValue){
            $query->where('identification_number','like','%'.$searchValue.'%')
            ->orWhere('NRIC_number','like','%'.$searchValue.'%')
            ->orWhereHas('user',function($query) use ($searchValue){
                $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
            });
        })
        ->orderBy('id','desc')
        ->get();
    }

    public function filter($searchValue){
        return $this->student->with('user','education','education.document')
        ->where('identification_number','like','%'.$searchValue.'%')
        ->orWhere('NRIC_number','like','%'.$searchValue.'%')
        ->orWhereHas('user',function($query) use ($searchValue){
            $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
        })
        ->orderBy('id','desc')
        ->paginate(10);
    }

    public function show($id){
        return $this->student->with('user','education','education.document')->find($id);
    }

    public function create(array $data){
        // $student = $this->student->where('user_id',$data['user_id'])->first();
        // if(!$student){
        // }
        $student = $this->student->where('user_id',$data['user_id'])->where('deleted_at',NULL)->first();
        if(!$student){
            $student = new Student();
        }
        
        $student->user_id = $data['user_id'] ?? NULL;
        $student->full_name = $data['full_name'] ?? NULL;
        $student->NRIC_number = $data['NRIC_number'] ?? NULL;
        // $student->identification_number = $identification_number;
        $student->nationality = $data['nationality'] ?? NULL;
        $student->date_of_birth = isset($data['date_of_birth']) ? date('Y-m-d',strtotime($data['date_of_birth'])) : NULL;
        $student->address = $data['address'] ?? NULL;
        $student->zip_code = $data['zip_code'] ?? NULL;
        $student->phone_number = $data['phone_number'] ?? NULL;
        $student->city = $data['city'] ?? NULL;
        $student->gender = $data['gender'] ?? NULL;
        $student->referral_id = $data['referral_id'] ?? NULL;
        $student->grant_applied_at = $data['grant_applied_at'] ?? NULL;

        if($student->save()){
            $student->identification_number = generateCustomStudentId($student->id);
            $student->save();
        }
        
        return $student->fresh();
    }

    public function update(array $data,$id){

        $student = $this->student->findOrFail($id);

        $student->user_id = $data['user_id'] ?? $student->user_id;
        $student->NRIC_number = $data['NRIC_number'] ?? $student->NRIC_number;
        // $student->identification_number = $identification_number;
        $student->nationality = $data['nationality'] ?? $student->nationality;
        $student->date_of_birth = isset($data['date_of_birth']) ? date('Y-m-d',strtotime($data['date_of_birth'])) : $student->date_of_birth;
        $student->address = $data['address'] ?? $student->address;
        $student->zip_code = $data['zip_code'] ?? $student->zip_code;
        $student->phone_number = $data['phone_number'] ?? $student->phone_number;
        $student->city = $data['city'] ?? $student->city;
        $student->gender = $data['gender'] ?? $student;
        $student->referral_id = $data['referral_id'] ?? $student->referral_id;
        $student->grant_applied_at = $data['grant_applied_at'] ?? $student->grant_applied_at;
        $student->identification_number = $student->identification_number ?? generateCustomStudentId($student->id);

        $student->save();
        return $student->fresh();
    }

    public function delete($id){
        $student = $this->student->findOrFail($id);
        if($student){
            $student->education()->delete();
            $user = $this->user->findOrFail($student->user_id);
            if($user->hasRole('student')){
                $user->removeRole('student');
            }
            return $student->delete();
        }else{
            throw new \Exception('Student not found');
        }
    }

    public function getStudentByUserId($user_id){
        return $this->student->where('user_id',$user_id)->first();
    }

    public function addEducation(array $data){
        $student = $this->student->findOrFail($data['student_id']);
        return $student->education()->create([
            'student_id' => $data['student_id'],
            'field_of_study' => $data['field_of_study'],
            'academic_year' => $data['academic_year'],
            'degree' => $data['degree']
        ]);
    }

    public function getEducation($id){
        $student = $this->student->findOrFail($id);
        return $student->education;
    }

    public function grantList($searchValue = NULL,$start_date = NULL,$end_date = NULL){
        $students = $this->student->with('user.referral')
        ->where('grant_applied_at' ,'!=', null)
        ->where('grant_approved_at',NULL)
        ->where('grant_rejected_at',NULL)
        ->when($searchValue,function($query) use ($searchValue){
            $query->where('identification_number','like','%'.$searchValue.'%')
            ->orWhere('NRIC_number','like','%'.$searchValue.'%')
            ->orWhereHas('user',function($query) use ($searchValue){
                $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
            });
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('created_at','<=',$end_date);
        })
        ->orderBy('created_at','desc')
        ->paginate(10);
        return $students;
    }

    public function grantHistoryList($searchValue = NULL,$start_date = NULL,$end_date = NULL){
        $students = $this->student->with('user')
        ->when($searchValue,function($query) use ($searchValue){
            $query->where('identification_number','like','%'.$searchValue.'%')
            ->orWhere('NRIC_number','like','%'.$searchValue.'%')
            ->orWhereHas('user',function($query) use ($searchValue){
                $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
            });
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('created_at','<=',$end_date);
        })
        ->where(function($query){
            $query->whereNotNull('grant_approved_at')
            ->orWhereNotNull('grant_rejected_at');
        })
        ->orderBy('created_at','desc')
        ->paginate(10);
        return $students;
    }

    public function grantConfirmation($data){
        $student = $this->student->with('user','grants')->where('id',$data['student_id'])->first();

        //Check if student grant already approved or rejected
        if(!$student){
            throw new \Exception('Student not found or not applied for grant');
        }else if(!is_null($student->grant_approved_at)){
            throw new \Exception('Student grant already approved');
        }else if(!is_null($student->grant_rejected_at)){
            throw new \Exception('Student grant already rejected');
        }

        if($data['is_approve']){
            $student->grant_approved_at = date('Y-m-d H:i:s');
        }else{
            $student->grant_rejected_at = date('Y-m-d H:i:s');
        }

        $student->save();
        return $student->fresh(); 
    }
}