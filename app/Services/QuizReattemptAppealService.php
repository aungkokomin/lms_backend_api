<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizReattemptAppeal;
use App\Models\StudentQuiz;
use App\Models\User;
use App\Notifications\QuizReattemptAppealNotification;
use Dflydev\DotAccessData\Exception\DataException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizReattemptAppealService
{
    // Add your repository methods here

    /**
     * Create a quiz re-attempt
     * @param $id
     * @param $student_id
     * @return mixed
     * @throws DataException
     */
    public function create($data){
        
        $student_id = $data['student_id'];
        $quiz_id = $data['quiz_id'];
        
        $appealExist = QuizReattemptAppeal::where('quiz_id',$quiz_id)->where('student_id',$student_id)->where('status','pending')->count();

        if($appealExist){
            throw new DataException("You have already submitted a re-attempt appeal for this quiz. Please wait for the decision.");
        }

        $attempt_count = StudentQuiz::where('quiz_id',$quiz_id)->where('student_id',$student_id)->where('status','failed')->count();
        // if($attempt_count >= 3 ){
        //     throw new DataException("Your attempts for this quiz has reached the maximum limit. Please contact your instructor for further assistance.");
        // }else{
            $result = QuizReattemptAppeal::create([
                'quiz_id' => $quiz_id,
                'student_id' => $student_id,
                'status' => QuizReattemptAppeal::STATUS_PENDING,
            ]);
            return $result;
        // }
    }

    /**
     * Get all quiz re-attempts
     * @return mixed
     */
    public function getPaginate($searchValue = null,$start_date = null,$end_date = null,$status = null){
        return QuizReattemptAppeal::with('student.user','quiz.lesson')
        ->when($searchValue,function($query) use ($searchValue){
            $query->whereHas('student.user',function($query) use ($searchValue){
                $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
            })
            ->orWhereHas('quiz.lesson',function($query) use ($searchValue){
                $query->where('title','like','%'.$searchValue.'%');
            });
        })
        ->when($status,function($query) use ($status){
            if($status == QuizReattemptAppeal::STATUS_PENDING){
                $query->where('status',QuizReattemptAppeal::STATUS_PENDING);
            }else{
                $query->where('status','!=',QuizReattemptAppeal::STATUS_PENDING);
            }
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('created_at','<=',$end_date);
        })
        ->orderByRaw('FIELD(status,"pending","approved","rejected") ASC')
        ->orderByDesc('created_at')
        ->paginate(10);
    }

    public function reattemptAppealDecision($id,$status){
        Log::info("id = ".$id."| status = ".$status);
        $reAttempt = QuizReattemptAppeal::with('quiz.lesson')->findOrFail($id);
        // dd($reAttempt);
        if($status == 'approved'){
            $reAttempt->status = QuizReattemptAppeal::STATUS_APPROVED;
            $reAttempt->approved_at = now();
        }else{
            $reAttempt->status = QuizReattemptAppeal::STATUS_REJECTED;
            $reAttempt->rejected_at = now();
        }
        $user_id = $reAttempt->student()->first()->user_id;
        $user = User::findOrFail($user_id);
        $student = $reAttempt->student()->orderByDesc('id')->first();
        $user->notify(new QuizReattemptAppealNotification($reAttempt->status,$reAttempt->quiz->lesson->title,$user,$student));
        $reAttempt->save();
        return $reAttempt;
    }

    public function getReattemptedCountOverList(){
        $reAttemptedOverList = DB::table('quiz_reattempt_appeals')->selectRaw('student_id, count(status) as reattempted_count, quiz_id')->where('status',QuizReattemptAppeal::STATUS_APPROVED)->groupBy('quiz_id','student_id')->get();
        
        return $reAttemptedOverList;
    }
}