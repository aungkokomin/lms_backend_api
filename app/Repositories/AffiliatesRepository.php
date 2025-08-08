<?php

namespace App\Repositories;

use App\Interfaces\AffiliatesRepositoryInterface;
use App\Models\Affiliator;
use App\Models\Referral;
use App\Models\Role;
use App\Models\User;

class AffiliatesRepository implements AffiliatesRepositoryInterface
{
    // Add your repository methods here
    protected $affiliates;
    protected $user;
    protected $referral;

    public function __construct(Affiliator $affiliates, User $user, Referral $referral) {
        $this->affiliates = $affiliates;
        $this->user = $user;
        $this->referral = $referral;
    }

    public function get($searchValue = NULL, $start_date = NULL, $end_date = NULL){
        return $this->affiliates->with('user','document')
        ->when($searchValue,function($query) use ($searchValue){
            $query->whereHas('user',function($query) use ($searchValue){
                $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
            });
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('created_at','<=',$end_date);
        })
        ->whereNot('affiliate_approved_at',NULL)
        ->orderByDesc('id')->paginate(10);
    }

    public function getUserList($searchValue = NULL, $start_date = NULL, $end_date = NULL){
        return $this->user->when($searchValue,function($query) use ($searchValue){
            $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('created_at','<=',$end_date);
        })
        ->whereHas('roles',function($query){
            $query->where('name',Role::ROLE_STUDENT)->orWhere('name',Role::ROLE_GUEST);
        })->orderByDesc('id')->paginate(10);
    }

    public function show($id){
        return $this->affiliates->with('user','commission')->findOrFail($id);
    }

    public function create(array $data){
        
        if($this->affiliates->where('user_id',$data['user_id'])->exists()){
            throw new \Exception("You are already an affiliate.");
        }
        if(!isset($data['referral_id'])){
            $user = $this->user->findOrFail($data['user_id']);
            $referral_id = $user->referral_id;
            $data['referral_id'] = $referral_id ?? null;
        }
        if(isset($data['affiliate_approved_at']) && $data['affiliate_approved_at']){
            $user->assignRole(Role::ROLE_AGENT);
        }
        return $this->affiliates->create($data);
    }

    public function update(array $data,int $id){
        $affiliates = $this->affiliates->findOrFail($id);
        return $affiliates->update($data);
    }

    public function delete($id){
        $affiliates = $this->affiliates->findOrFail($id);
        $user = $this->user->findOrFail($affiliates->user_id);
        $user->removeRole(Role::ROLE_AGENT);
        return $affiliates->delete();
    }

    public function getStudentList($referral_id,$searchValue = NULL,$start_date = NULL,$end_date = NULL){
        $refer_student = $this->referral->with('user.student')
        ->when($searchValue,function($query) use ($searchValue){
            $query->whereHas('user',function($query) use ($searchValue){
                $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
            });
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('created_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('created_at','<=',$end_date);
        })
        ->whereHas('user',function($query){
            $query->whereHas('student',function($query){
                $query->where('deleted_at',NULL);
            });
        })
        ->where('referrer_id',$referral_id)
        ->orderBy('id','desc')
        ->paginate(10);

        return $refer_student;
    }

    public function searchUserList($searchValue){
        $user = $this->user->where('email',$searchValue)
        ->orWhere('name',$searchValue)
        ->whereHas('roles',function($query){
            $query->where('name',Role::ROLE_STUDENT)->orWhere('name',Role::ROLE_GUEST);
        })
        ->orderByDesc('id')
        ->paginate(10);
        
        return $user;
    }

    public function searchAffiliates($searchValue){
        return $this->affiliates->with('user')->whereHas('user',function($query) use ($searchValue){
            $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
        })
        ->orderBy('id','desc')
        ->paginate(10);
    }

    public function getAffiliateApplications($searchValue = NULL, $start_date = NULL, $end_date = NULL)
    {
        return $this->affiliates->with('user','document')
        ->where('affiliate_approved_at',NULL)
        ->where('affiliate_rejected_at',NULL)
        ->when($searchValue,function($query) use ($searchValue){
            $query->whereHas('user',function($query) use ($searchValue){
                $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
            });
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('affiliate_applied_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('affiliate_applied_at','<=',$end_date);
        })
        ->orderByDesc('id')->paginate(10);
    }

    public function getAffiliateApplicationsRejectList($searchValue = NULL, $start_date = NULL, $end_date = NULL)
    {
        return $this->affiliates->with('user','document')
        ->where('affiliate_approved_at',NULL)
        ->where('affiliate_rejected_at','!=',NULL)
        ->when($searchValue,function($query) use ($searchValue){
            $query->whereHas('user',function($query) use ($searchValue){
                $query->where('name','like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%');
            });
        })
        ->when($start_date,function($query) use ($start_date){
            $query->whereDate('affiliate_rejected_at','>=',$start_date);
        })
        ->when($end_date,function($query) use ($end_date){
            $query->whereDate('affiliate_rejected_at','<=',$end_date);
        })->orderByDesc('id')->withTrashed()->paginate(10);
    }

    public function confirmationAffiliateApplication(int $id,bool $status)
    {
        $affiliate = $this->affiliates->findOrFail($id);

        if(!$status)
        {
            $affiliate->affiliate_rejected_at = now();
            $affiliate->save();
            $url = $affiliate->document ? $affiliate->document->url : null;
            $url != NULL ?? unlink($url);  // Delete Document file if rejected
            $affiliate->document()->delete();  // Delete document data if rejected 
            return $affiliate->delete();
        }

        $affiliate->affiliate_approved_at = now();
        $user = User::findOrFail($affiliate->user_id);
        $user->assignRole(Role::ROLE_AGENT);  // Assign agent role to user

        if($user->hasRole(Role::ROLE_GUEST))  // Check if user has guest role
        {
            $user->removeRole(Role::ROLE_GUEST);
        }
        $affiliate->save();
        return $affiliate->fresh(); 
    }

}