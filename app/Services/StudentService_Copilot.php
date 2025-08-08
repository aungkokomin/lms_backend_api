<?php
namespace App\Services;

use App\Helpers\EducationAdditionHelper;
use App\Helpers\EducationHelper;
use App\Helpers\GrantHelper;
use App\Helpers\StudentGrantHelper;
use App\Http\Resources\StudentResource;
use App\Interfaces\StudentRepositoryInterface;
use App\Models\LessonProgress;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use function App\Helpers\referrerValidCheck;

class StudentService
{
    protected $studentRepositoryInterface;
    protected $educationHelper;
    protected $grantHelper;
    protected $role;

    public function __construct(
        StudentRepositoryInterface $studentRepositoryInterface,
        EducationAdditionHelper $educationHelper,
        StudentGrantHelper $grantHelper
    ) {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
        $this->educationHelper = $educationHelper;
        $this->grantHelper = $grantHelper;
        $this->role = 'student';
    }

    public function studentList($searchValue = null, $start_date = null, $end_date = null)
    {
        return $this->studentRepositoryInterface->list($searchValue, $start_date, $end_date);
    }

    public function studentFilter($searchValue)
    {
        return $this->studentRepositoryInterface->filter($searchValue);
    }

    public function createStudent($data)
    {
        if (isset($data['email'])) {
            $user = User::where('email', $data['email'])->firstOrFail();
        }
        Log::info(json_encode($data));
        $data['user_id'] = $user->id;
        $data['referral_id'] = $user->referral_id;

        if ($user->hasRole('student')) {
            throw new \Exception('User already a student');
        }
        if (isset($data['grant_apply']) && $data['grant_apply']) {
            $data['grant_applied_at'] = date('Y-m-d H:i:s');
        } else {
            $data['grant_applied_at'] = null;
        }

        $student = $this->studentRepositoryInterface->create($data);
        if ($student) {
            $this->handleReferral($data, $student);
            $data['student_id'] = $student->id;
            $this->educationHelper->addEducationInfo($data);
        }

        return $student;
    }

    protected function handleReferral($data, $student)
    {
        if (!isset($data['referrer_id']) || !$data['referrer_id']) {
            $referrer_code = User::role('admin')->first()->referral_id;
        } else {
            if (!referrerValidCheck($data['referrer_id'])) {
                throw new InvalidArgumentException('Invalid Referrer ID Provided');
            }
            $referrer_code = $data['referrer_id'];
        }

        if ($referrer_code) {
            $referral = Referral::where('user_id', $student->user_id)->first();
            if ($referral) {
                $referral->update([
                    'referrer_id' => $referrer_code
                ]);
            } else {
                Referral::create([
                    'user_id' => $student->user_id,
                    'referrer_id' => $referrer_code
                ]);
            }
        }
    }

    public function update($data, $id)
    {
        $student = $this->studentRepositoryInterface->show($id);
        $student->update($data);
        return $student;
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

    public function delete($id)
    {
        return $this->studentRepositoryInterface->delete($id);
    }

    public function getStudentByUserId($user_id)
    {
        return $this->studentRepositoryInterface->getStudentByUserId($user_id);
    }

    public function grantApplyList($searchValue = null, $start_date = null, $end_date = null)
    {
        return $this->studentRepositoryInterface->grantList($searchValue, $start_date, $end_date);
    }

    public function grantHistoryList($searchValue = null, $start_date = null, $end_date = null)
    {
        return $this->studentRepositoryInterface->grantHistoryList($searchValue, $start_date, $end_date);
    }

    public function grantConfirmation($data)
    {
        return $this->grantHelper->grantConfirmation($data);
    }
}