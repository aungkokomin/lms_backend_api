<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCertificateResource;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    //
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Get all certificates
     * @return mixed
     */
    public function index()
    {
        return response()->json([
            'data' => $this->certificateService->get(),
            'status' => 200
        ], 200);
    }

    /**
     * Create a new certificate
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        
        try{
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:certifications',
                'course_id' => 'sometimes|nullable|integer|exists:courses,id',
                'module_id' => 'sometimes|nullable|integer|exists:modules,id',
                'description' => 'required|string',
                'requirements' => 'required|string',
                'level' => 'required|string',
                'issue_authority' => 'required|string',
                'validity_period' => 'required|string|',
            ]);

            return response()->json([
                'data' => $this->certificateService->create($data),
                'status' => 201
            ], 201);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a certificate by id
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        try{
            $certificate = $this->certificateService->show($id);
            return response()->json([
                'data' => $certificate,
                'status' => 200
            ], 200);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a certificate
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function update(Request $request, int $id)
    {
        try{
            $data = $request->validate([
                'name' => 'sometimes|string|max:255|unique:certifications,name,' . $id,
                'course_id' => 'sometimes|nullable|integer|exists:courses,id',
                'module_id' => 'sometimes|nullable|integer|exists:modules,id',
                'description' => 'sometimes|string',
                'requirements' => 'sometimes|string',
                'level' => 'sometimes|string',
                'issue_authority' => 'sometimes|string',
                'validity_period' => 'sometimes|string|',
            ]);
            
            return response()->json([
                'data' => $this->certificateService->update($data, $id),
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a certificate
     * @param int $id
     * @return mixed
     */
    public function destroy($id)
    {
        $certificate = $this->certificateService->delete($id);
        return response()->json([
            'data' => $certificate, 
            'message' => 'Successfully deleted!', 
            'status' => 200
        ], 200);
    }

    /**
     * Get certificates by course
     * @param int $course_id
     * @return mixed
     */
    public function getCertificateByCourse($course_id)
    {
        return response()->json([
            'data' => $this->certificateService->getCertificateByCourse($course_id),
            'status' => 200
        ], 200);
    }

    /**
     * Get certificates by module
     * @param int $module_id
     * @return mixed
     */
    public function getCertificateByModule($module_id)
    {
        return response()->json([
            'data' => $this->certificateService->getCertificateByModule($module_id),
            'status' => 200
        ], 200);
    }

    // public function download($id)
    // {
    //     $certificate = $this->certificateService->show($id);
    //     return response()->download(storage_path('app/' . $certificate->file));
    // }

    // public function search(Request $request)
    // {
    //     $certificates = $this->certificateService->search($request->search);
    //     return response()->json($certificates);
    // }

    /**
     * Request a certificate
     * @param Request $request
     * @return mixed
     */
    // public function requestCertificate(Request $request)
    // {
    //     try {
    //         $data = $request->validate([
    //             'student_id' => 'required|integer|exists:students,id',
    //             'certificate_id' => 'required|integer|exists:certifications,id',
    //             'student_name' => 'sometimes|string',
    //             'student_email' => 'sometimes|email',
    //             'NRIC_number' => 'sometimes|string',
    //         ]);

    //         $requestCertificate = $this->certificateService->requestCertificate($data);

    //         return response()->json([
    //             'data' => $requestCertificate,
    //             'status' => 201,
    //             'message' => 'Certificate request sent successfully!'
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage()], 500);
    //     }
    // }

    public function studentCertificateList(Request $request)
    {
        try{
            $data = $request->validate([
                'searchValue' => 'sometimes|string|nullable',
                'start_date' => 'sometimes|date|nullable',
                'end_date' => 'sometimes|date|nullable',
                'status' => 'sometimes|string|nullable'
            ]);
            $searchValue = $data['searchValue'] ?? null;
            $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
            $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
            $status = $data['status'] ?? null;
            $certificates = $this->certificateService->studentCertificateList($searchValue,$start_date,$end_date,$status);
            return response()->json([
                'data' => UserCertificateResource::collection($certificates),
                'currentPage' => $certificates->currentPage(),
                'lastPage' => $certificates->lastPage(),
                'perPage' => $certificates->perPage(),
                'total' => $certificates->total(),
                'status' => 200
            ], 200);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function uploadCertificate(Request $request, $id)
    {
        try{
            $data = $request->validate([
                // 'student_id' => 'required|integer|exists:students,id',
                'certificate_file' => 'required|file|mimes:png,jpeg,pdf',
            ]);

            return response()->json([
                'data' => $this->certificateService->uploadCertificate($data,$id),
                'status' => 201
            ], 201);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function downloadCertificate($id)
    {
        $certificate = $this->certificateService->show($id);
        return response()->download($certificate->certificateFile->url);
    }

    public function getCertificatesByStudent()
    {
        return response()->json([
            'data' => UserCertificateResource::collection($this->certificateService->getCertificateByUser()),
            'status' => 200
        ], 200);
    }
}
