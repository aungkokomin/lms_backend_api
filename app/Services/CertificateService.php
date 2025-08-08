<?php

namespace App\Services;

use App\Interfaces\CertificateRepositoryInterface;
use App\Models\Student;
use App\Models\UserCertification;
use Illuminate\Support\Facades\Auth;

use function App\Helpers\storeFile;

class CertificateService
{
    // Add your repository methods here
    protected $certificateRepositoryInterface;

    public function __construct(CertificateRepositoryInterface $certificateRepositoryInterface)
    {
        $this->certificateRepositoryInterface = $certificateRepositoryInterface;
    }

    /**
     * Get all certificates
     * @return mixed
     */
    public function get()
    {
        return $this->certificateRepositoryInterface->get();
    }

    /**
     * Create a new certificate
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->certificateRepositoryInterface->create($data);
    }

    /**
     * Get a certificate by id
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        return $this->certificateRepositoryInterface->show($id);
    }

    /**
     * Update a certificate
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function update(array $data, int $id)
    {
        return $this->certificateRepositoryInterface->update($data, $id);
    }

    /**
     * Delete a certificate
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        return $this->certificateRepositoryInterface->delete($id);
    }

    /**
     * Get certificates by course
     * @param int $course_id
     * @return mixed
     */
    public function getCertificateByCourse(int $course_id)
    {
        return $this->certificateRepositoryInterface->getCertificateByCourse($course_id);
    }

    /**
     * Get certificates by module
     * @param int $module_id
     * @return mixed
     */
    public function getCertificateByModule(int $module_id)
    {
        return $this->certificateRepositoryInterface->getCertificateByModule($module_id);
    }

    /**
     * Request a certificate
     * 
     * @param Request $request
     * @return mixed
     */
    public function requestCertificate($request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        return $this->certificateRepositoryInterface->create($data);
    }

    /**
     * Summary of studentCertificateList
     * 
     * @return mixed
     */
    public function studentCertificateList($searchValue = NULL, $start_date = NULL, $end_date = NULL,$status = NULL)
    {
        return $this->certificateRepositoryInterface->studentCertificateList($searchValue, $start_date, $end_date,$status);
    }

    /**
     * Summary of uploadCertificate
     * 
     * @param mixed $data 
     * @param mixed $id 
     * @return mixed
     */
    public function uploadCertificate($data,$id)
    {
        if(isset($data['certificate_file']))
        {
            $path = storeFile($data['certificate_file'], 'student/certificates');
            
            $certificate = UserCertification::find($id);
            if($certificate->certificateFile()->exists()){
                $url = $certificate->certificateFile()->first()->url;
                $url != NULL ?? unlink($url);
                $certificate->certificateFile()->delete();
            }
            $certificate->certificateFile()->create([
                'url' => $path['url']
            ]);
            $data['certificate_file'] = $path['fileNameWithoutExtension'];
            $data['status'] = UserCertification::STATUS_ISSUED;
            $data['issue_date'] = now();
            $data['expiry_date'] = now()->addYear();
        }
        
        return $this->certificateRepositoryInterface->update($data,$id);
    }

    public function getCertificateByUser()
    {
        $user = Auth::user();
        if(!$user->hasRole('student')){
            throw new \Exception('User is not a student');
        }
        
        $student = Student::where('user_id',$user->id)->first();

        return $this->certificateRepositoryInterface->getCertificateByStudent($student->id);
    }
}