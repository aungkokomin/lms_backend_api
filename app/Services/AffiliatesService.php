<?php

namespace App\Services;

use App\Exports\AffiliateFilterExport;
use App\Interfaces\AffiliatesRepositoryInterface;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Notifications\AffiliateApplicationNotification;
use App\Notifications\AffiliateApplyConfirmaNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use function App\Helpers\storeFile;
use function App\Helpers\writeTypeIdentifier;

class AffiliatesService
{
    protected $affiliatesRepositoryInterface;

    public function __construct(AffiliatesRepositoryInterface $affiliatesRepositoryInterface) {
        $this->affiliatesRepositoryInterface = $affiliatesRepositoryInterface;
    }

    public function all($searchValue = null,$start_date = null,$end_date = null){
        return $this->affiliatesRepositoryInterface->get($searchValue,$start_date,$end_date);
    }

    public function getUserList($searchValue = null,$start_date = null,$end_date = null){
        return $this->affiliatesRepositoryInterface->getUserList($searchValue,$start_date,$end_date);
    }

    public function searchInUserList($searchValue = null){
        return $this->affiliatesRepositoryInterface->searchUserList($searchValue);
    }

    public function find($id){
        return $this->affiliatesRepositoryInterface->show($id);
    }

    public function create(array $data){
        return $this->affiliatesRepositoryInterface->create($data);
    }

    public function update(array $data,int $id){

        return $this->affiliatesRepositoryInterface->update($data,$id);
    }

    public function delete($id){
        return $this->affiliatesRepositoryInterface->delete($id);
    }

    public function getStudentList($searchValue = NULL,$start_date = NULL,$end_date = NULL){
        $user = Auth::user();
        // if(!$user->hasRole(Role::ROLE_AGENT)){
        //     throw new \Exception('You are not authorized to access this resource');
        // }
        $referral_id = $user->referral_id;
        return $this->affiliatesRepositoryInterface->getStudentList($referral_id,$searchValue,$start_date,$end_date);
    }

    public function searchAffiliate($searchValue){
        return $this->affiliatesRepositoryInterface->searchAffiliates($searchValue);
    }

    public function downloadAffiliateAssigneeList($searchValue = NULL,$start_date = NULL,$end_date = NULL,$format = 'csv'){
        $writeType = writeTypeIdentifier($format ?? 'csv');
        $filename = 'public/excel/affiliators_assign_list.'.$format.'';
        $result = Excel::store(new AffiliateFilterExport($searchValue,$start_date,$end_date), $filename,null,$writeType);

        if($result){
            return Storage::url($filename);
        }else{
            throw new \Exception('Failed to download excel');
        }
    }

    /**
     * Summary of saveAffiliateApplication
     * @param array $data
     * @return object $affiliate
     */
    public function saveAffiliateApplication(array $data){
        
        try{
            // if(isset($data['student_id_number']) && $data['student_id_number']){
            //     $student = Student::where('identification_number', $data['student_id_number'])->first();
            //     if(!$student){
            //         throw new \Exception('Student not found');
            //     }
            // }
            $data['user_id'] = Auth::user()->id;
            $user = User::find($data['user_id']);

            if(!$user){
                throw new \Exception('User not found');
            }

            if($user->hasRole(Role::ROLE_AGENT)){
                throw new \Exception('You are already an agent');
            }
            
            $data['affiliate_applied_at'] = now();
            $affiliate = $this->affiliatesRepositoryInterface->create($data);
            if(isset($data['id_document']) && $data['id_document']){
                $paths = storeFile($data['id_document'],'affiliate/id_documents');
                $affiliate->document()->create([
                    'url' => $paths['url']
                ]);
            }
            // Send Notification to Admin
            $user = User::role(Role::ROLE_ADMIN)->first();
            $user->notify(new AffiliateApplicationNotification($affiliate));

            return $affiliate;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    // /**
    //  * Summary of applyAffiliateViaStudent
    //  * 
    //  * @param int $student_id
    //  * @return mixed
    //  */
    // public function applyAffiliateViaStudent($student_id_number){
    //     $student = Student::where('identification_number', $student_id_number)->first();
    //     if(!$student){
    //         throw new \Exception('Student not found');
    //     }

    //     $user = User::find($student->user_id);

    //     if(!$user){
    //         throw new \Exception('User not found');
    //     }

    //     if($user->hasRole(Role::ROLE_AGENT)){
    //         throw new \Exception('You are already an agent');
    //     }
    //     $data = [
    //         'user_id' => $user->id,
    //         'referral_id' => $user->referral_id,
    //         'full_name' => $student->full_name,
    //         'identification_no' => $student->NRIC_number,
    //         'phone_number' => $student->phone_number,
    //         'affiliate_applied_at' => now(),
    //     ];
    //     $affiliate = $this->affiliatesRepositoryInterface->create($data);
    //     $url = $student->document()->first()->url?? null;
    //     if($url){
    //         $affiliate->document()->create(['url' => $url]);
    //     }
    //     return $affiliate;
    // }

    /**
     * Summary of getAffiliateApplications
     */
    public function getAffiliateApplications($searchValue = NULL, $start_date = NULL, $end_date = NULL){
        $affiliates = $this->affiliatesRepositoryInterface->getAffiliateApplications($searchValue, $start_date, $end_date);
        return $affiliates;
    }

    /**
     * Summary of confirmationAffiliateApplication
     * 
     * @param int $id
     * @param bool $status
     * @return $affiliate
     */
    public function confirmationAffiliateApplication($id, $status){
        $affiliate = $this->affiliatesRepositoryInterface->confirmationAffiliateApplication($id, $status);
        if($affiliate->affiliate_approved_at){
            $user = User::findOrFail($affiliate->user_id);
            $user->notify(new AffiliateApplyConfirmaNotification($affiliate));
        }
        return $affiliate;
    }

    /**
     * Summary of getAffiliateApplicationsRejectList
     * 
     * @param string $searchValue
     * @param string $start_date
     * @param string $end_date
     * @return mixed
     */
    public function getAffiliateApplicationsRejectList($searchValue = NULL, $start_date = NULL, $end_date = NULL){
        $affiliates = $this->affiliatesRepositoryInterface->getAffiliateApplicationsRejectList($searchValue, $start_date, $end_date);
        return $affiliates;
    }
}