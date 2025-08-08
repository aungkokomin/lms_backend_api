<?php

namespace App\Helpers;

use App\Models\Affiliator;
use App\Models\Commission;
use App\Models\Referral;
use App\Models\Student;
use App\Models\User;
use App\Models\UserGrantCode;
use Illuminate\Support\Facades\Log;
use PDO;

if(!function_exists('getUniqueReferralId')){
    function getUniqueReferralId(){

    }
}

if(!function_exists('referrerValidCheck')){
    function referrerValidCheck($id) {
        $check = User::where('referral_id', $id)->count();
        return $check;
    }
}

if(!function_exists('storeFile'))
{
    function storeFile(object $file,string $folder)
    {
        $fileNameWithoutExtension = preg_replace(
            '/[^A-Za-z0-9\-]/',
            '_',
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $originalName = preg_replace('/[^A-Za-z0-9\-]/', '_',$file->getClientOriginalName());
        $fileName = time().'.'.$file->getClientOriginalExtension();
        $result = [
            'fileNameWithoutExtension' => $fileNameWithoutExtension,
            'originalName' => $originalName,
            'fileName' => $fileName,
            'filePath' => $file->storeAs('public/'.$folder,$fileName),
            'url' => "storage/$folder/$fileName"
        ];
        return $result;
    }
}

if(!function_exists('assignUserRole'))
{
    function assignUserRole($user_id,$role)
    {
        $user = User::find($user_id);
        
        if($user->hasRole($role)){
            $message = "User already has $role role";
        }else if (!$user->hasRole($role)){
            $user->assignRole($role);
            $message = "$role Role assigned successfully";
        }

        return [
            'user' => $user,
            'role' => $user->getRoleNames(),
            'message' => $message ? $message : "$role Role not assigned"
        ];
    }
}

if(!function_exists('generateUniqueCode')){
    function generateUniqueCode(int $length = 6)
    {

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);
        $codeLength = $length ?? 6;

        $code = '';

        while (strlen($code) < $codeLength) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;
        }

        return $code;

    }
}

if(!function_exists("affiliateCommission")){
    function affiliateCommission (int $user_id,int $payment_amount){
        $referral = Referral::where('user_id',$user_id)->first();
        Log::info('Referral: ' . json_encode($referral));
        if($referral){
            $affiliator = Affiliator::where('referral_id',$referral->referrer_id)->first();
            Log::info('Affiliator: ' . json_encode($affiliator));
            if($affiliator){
                $own_commission_rate_used_flg = $affiliator->commission_rate ? 'true' : 'false';
                $commission_rate = $affiliator->commission_rate ?? Commission::DEFAULT_COMMISSION_RATE;
                Log::info('Commission Rate: ' . $commission_rate);
            }else{
                $own_commission_rate_used_flg = 'false';
                $commission_rate = Commission::DEFAULT_COMMISSION_RATE;
                Log::info('Commission Rate: ' . $commission_rate);
            }
            
            Commission::create([
                'referral_id' => $referral->referrer_id,
                'user_id' => $user_id,
                'commission_amount' => $commission_rate ? $payment_amount * ($commission_rate / 100) : 0,
                'applied_commission_rate' => $commission_rate,
                'payment_amount' => $payment_amount,
                'used_own_commission_rate' => $own_commission_rate_used_flg
            ]);
        }
    }
}

if(!function_exists('generateCustomStudentId')){
    function generateCustomStudentId($last_student_id = NULL){
        $last_student_id = $last_student_id ?? Student::withTrashed()->orderBy('id','desc')->first()->id + 1;
        if(strlen($last_student_id) == 1){
            $last_student_id = '00'.($last_student_id);
        }else if(strlen($last_student_id) == 2){
            $last_student_id = '0'.($last_student_id);
        }else{
            $last_student_id = ($last_student_id);
        }
        $year = substr(date('Y'),1);

        $student_id = '117'.$last_student_id.$year;
        return $student_id;
    }
}

if(!function_exists('writeTypeIdentifier')){
    function writeTypeIdentifier(?string $format = NULL)
    {
        if($format){
            if($format == 'csv'){
                $writeType = \Maatwebsite\Excel\Excel::CSV;
            }else if($format == 'xlsx'){
                $writeType = \Maatwebsite\Excel\Excel::XLSX;
            }else if($format == 'xls'){
                $writeType = \Maatwebsite\Excel\Excel::XLS;
            }else if($format == 'pdf'){
                $writeType = \Maatwebsite\Excel\Excel::DOMPDF;
            }
        }else{
            $writeType = \Maatwebsite\Excel\Excel::CSV;
        }
        return $writeType;
    }
}