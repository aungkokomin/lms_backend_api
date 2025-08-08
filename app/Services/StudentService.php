<?php

namespace App\Services;

use App\Exports\StudentFilterExport;
use App\Interfaces\StudentRepositoryInterface;
use App\Mail\StudentGrantApproved;
use App\Mail\StudentRegistration;
use App\Models\LessonProgress;
use App\Models\Referral;
use App\Models\Student;
use App\Models\StudentEnrollPayments;
use App\Models\User;
use App\Models\UserGrantCode;
use App\Notifications\StudentGrantNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use Stripe\StripeClient;
use function App\Helpers\generateUniqueCode;
use function App\Helpers\referrerValidCheck;
use function App\Helpers\storeFile;
use function App\Helpers\writeTypeIdentifier;

class StudentService
{
    // Add your repository methods here
    protected $studentRepositoryInterface;
    protected $role;

    /**
     * Summary of __construct
     * @param \App\Interfaces\StudentRepositoryInterface $studentRepositoryInterface
     */
    public function __construct(StudentRepositoryInterface $studentRepositoryInterface) {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
        $this->role = 'student';
    }

    /**
     * Summary of studentList
     * @return mixed
     */
    public function studentList($searchValue = null,$start_date = null,$end_date = null){
        return $this->studentRepositoryInterface->list($searchValue,$start_date,$end_date);
    }

    /**
     * Summary of studentFilter
     * @param mixed $searchValue 
     * @return mixed
     */
    public function studentFilter($searchValue){
        return $this->studentRepositoryInterface->filter($searchValue);
    }

    public function createStudent($data)
    {
        if(isset($data['email'])){
            $user = User::where('email',$data['email'])->firstOrFail();
        }
        Log::info(json_encode($data));
        $data['user_id'] = $user->id;
        $data['referral_id'] = $user->referral_id;

        if($user->hasRole('student')){
            throw new \Exception('User already a student');
        }
        if(isset($data['grant_apply']) && $data['grant_apply']){
            $data['grant_applied_at'] = date('Y-m-d H:i:s');
        }else{
            $data['grant_applied_at'] = null;
        }

        $student =  $this->studentRepositoryInterface->create($data);
        if($student){
            if(!isset($data['referrer_id']) || !$data['referrer_id']){
                $referrer_code = User::role('admin')->first()->referral_id;
            }else {
                if(!referrerValidCheck($data['referrer_id'])){
                    throw new InvalidArgumentException('Invalid Referrer ID Provided');
                }
                $referrer_code = $data['referrer_id'];
            }

            if($referrer_code){
                $referral = Referral::where('user_id',$student->user_id)->first();
                if($referral){
                    $referral->update([
                        'referrer_id' => $referrer_code
                    ]);
                }else{
                    Referral::create([
                        'user_id' => $student->user_id,
                        'referrer_id' => $referrer_code
                    ]);
                }
            }

            $data['student_id'] = $student->id;
            $this->addEducationInfo($data);
            // $user->assignRole($this->role);

            // Mail::to($user->email)->send(new StudentRegistration($user));
        }

        return $student;

    }

    public function addEducationInfo($data){
        if(isset($data['education'])){
            $education = json_decode($data['education'],true);

            if(json_last_error() !== JSON_ERROR_NONE){
                throw new \Exception('Invalid education data');
            }
            $i = 0;
            foreach($education as $edu){
                $edu['student_id'] = $data['student_id'];
                $result = $this->studentRepositoryInterface->addEducation($edu);
                if(isset($data['edu_doc'][$i]) && !empty($data['edu_doc'][$i])){
                    $paths = storeFile($data['edu_doc'][$i],'student/education/docs');
                    $result->document()->create([
                        'url' => $paths['url']
                    ]);
                }
                
                $i++;
            }
        }
        return true;
    }

    public function show($id)
    {
        $student = $this->studentRepositoryInterface->show($id);
        if(!$student){
            throw new \Exception('Student not found');
        }else{
            $lessonProgress = LessonProgress::where('student_id',$student->id)->orderBy('completed_at','desc')->get();
            
            $lessonProgress->map(function($progress){
                if($progress->lesson){
                    $progress->lesson_title = $progress->lesson->title;
                    $progress->module_title = $progress->lesson->module->title;
                    $progress->course_title = $progress->lesson->module->course->title;
                }
            });
            $student->lesson_progress = $lessonProgress;
        }
        return $student;
    }

    public function update($data,$id)
    {
        $student = $this->studentRepositoryInterface->show($id);
        $student->update($data);
        return $student;
    }

    public function delete($id)
    {
        return $this->studentRepositoryInterface->delete($id);
    }

    public function getStedentByUserId($user_id)
    {
        return $this->studentRepositoryInterface->getStudentByUserId($user_id);
    }

    public function grantApplyList($searchValue = null,$start_date = null,$end_date = null)
    {
        return $this->studentRepositoryInterface->grantList($searchValue,$start_date,$end_date);
    }

    public function grantHistoryList($searchValue = null,$start_date = null,$end_date = null)
    {
        return $this->studentRepositoryInterface->grantHistoryList($searchValue,$start_date,$end_date);
    }

    public function grantConfirmation($data)
    {
        $student = $this->studentRepositoryInterface->grantConfirmation($data);

        // Check if student grant already approved or rejected
        if($student){
            $stripe = new StripeClient(config('services.stripe.secret'));
            // Create a coupon code for student
            if($stripe){
                $code = $stripe->coupons->create([
                    'percent_off' => UserGrantCode::DEFAULT_GRANT_AMOUNT,
                    'duration' => 'once',
                    'name' => $student->user ? $student->user->name : 'Student Grant - '.$student->id,
                    'currency' => 'usd'
                ])->id;
                $grant_type = UserGrantCode::GRANT_TYPE_STRIPE;
            }else{
                // Generate a unique code in Local
                $code = generateUniqueCode(6);
                while(UserGrantCode::where('code', $code)->exists()) {
                    $code = generateUniqueCode(6);
                }
                $grant_type = UserGrantCode::GRANT_TYPE_CRYPTO;
            }
            // Create a grant code for student
            $userGrantCode = UserGrantCode::create([
                'student_id' => $student->id,
                'code' => $code,
                'grant_amount' => UserGrantCode::DEFAULT_GRANT_AMOUNT,
                'grant_type' => $grant_type,
                'expired_at' => date('Y-m-d H:i:s', strtotime('+3 months')),
                'is_active' => true
            ]);
            $userGrantCode->fresh();
            if($userGrantCode){
                $message = 'Student grant approved! Your grant code is : '.$userGrantCode->code;
                $user = User::find($student->user_id);
                $user->notify(new StudentGrantNotification($userGrantCode,$message,$student->user_id));
                // Mail::to($student->user->email)->send(new StudentGrantApproved($student->user,$userGrantCode));
            }
        }
        return $student->fresh();
    }

    /**
     * Summary of downloadExcel
     * @param mixed $searchValue 
     * @param mixed $start_date 
     * @param mixed $end_date 
     * @param mixed $format 
     * @return mixed
     */
    public function downloadExcel($searchValue = null,$start_date = null,$end_date = null,$format = 'csv')
    {
        $writeType = writeTypeIdentifier($format ?? 'csv');
        $filePath = 'public/excel/students_list.'.strtotime(date('Y-m-d H:m:i')).'.'.$format.'';
        $result = Excel::store(new StudentFilterExport($searchValue,$start_date,$end_date), $filePath,null,$writeType);

        if($result){
            return Storage::url($filePath);
        }else{
            throw new \Exception('Failed to download excel');
        }
    }
}