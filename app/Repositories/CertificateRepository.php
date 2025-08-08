<?php

namespace App\Repositories;

use App\Interfaces\CertificateRepositoryInterface;
use App\Models\Certificates;
use App\Models\UserCertification;

class CertificateRepository implements CertificateRepositoryInterface
{
    // Add your repository methods here

    protected $userCertificate;

    public function __construct(UserCertification $userCertificates)
    {
        $this->userCertificate = $userCertificates;
    }
    
    public function get()
    {
        $certificates = $this->userCertificate->paginate(10);
        return $certificates;
    }

    public function create(array $data)
    {
        return $this->userCertificate->create([
            'student_id' => $data['student_id'],
            'course_id' => $data['course_id'] ?? null,
            'module_id' => $data['module_id'] ?? null,
            'issue_date' => $data['issue_date'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'status' => $data['status'] ?? null,
            'gpa_score' => $data['gpa_score'] ?? null,
            'grade' => $data['grade'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);
    }

    public function show(int $id)
    {
        $certificate = $this->userCertificate
        ->with('student.user','certificateFile','module','course')
        ->whereHas('student', function($query) use ($id){
            $query->where('deleted_at',NULL);
        })
        ->find($id);
        return $certificate;
    }

    public function update(array $data, int $id)
    {
        $certificate = $this->userCertificate->find($id);
        return $certificate->update([
            'student_id' => $data['student_id'] ?? $certificate->student_id,
            'course_id' => $data['course_id'] ?? $certificate->course_id,
            'module_id' => $data['module_id'] ?? $certificate->module_id,
            'issue_date' => $data['issue_date'] ?? $certificate->issue_date,
            'expiry_date' => $data['expiry_date'] ?? $certificate->expiry_date,
            'status' => $data['status'] ?? $certificate->status,
            'gpa_score' => $data['gpa_score'] ?? $certificate->gpa_score,
            'grade' => $data['grade'] ?? $certificate->grade,
            'notes' => $data['notes'] ?? $certificate->notes,
            'certificate_file' => $data['certificate_file'] ?? $certificate->certificate_file
        ]);
    }

    public function delete(int $id)
    {
        $certificate = $this->userCertificate->find($id);
        return $certificate->delete();
    }

    public function getCertificateByCourse(int $course_id)
    {
        $certificates = $this->userCertificate->where('course_id', $course_id)->get();
        return $certificates;
    }

    public function getCertificateByModule(int $module_id)
    {
        $certificates = $this->userCertificate->where('module_id', $module_id)->get();
        return $certificates;
    }

    public function studentCertificateList($searchValue = NULL, $start_date = NULL, $end_date = NULL, $status = NULL)
    {
        $certificates = $this->userCertificate
        ->when($searchValue, function($query) use ($searchValue){
            return $query->whereHas('student', function($query) use ($searchValue){
                $query->where('full_name','like','%'.$searchValue.'%')
                ->orWhereHas('user', function($query) use ($searchValue){
                    $query->where('email',$searchValue);
                });
            });
        })
        ->when($start_date, function($query) use ($start_date){
            return $query->where('created_at', '>=', $start_date);
        })
        ->when($end_date, function($query) use ($end_date){
            return $query->where('created_at', '<=', $end_date);
        })
        ->with('student','student.user','certificateFile','module','course')
        ->when($status, function($query) use ($status){
            return $query->where('status', $status);
        })
        ->orderBy('created_at','desc')
        ->paginate(10);
        return $certificates;
    }

    public function getCertificateByStudent(int $student_id)
    {
        $certificates = $this->userCertificate->where('student_id', $student_id)->get();
        return $certificates;
    }
}