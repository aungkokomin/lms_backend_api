<?php

namespace App\Http\Controllers\api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AffiliateApplicationResource;
use App\Http\Resources\AffiliateResource;
use App\Http\Resources\ReferStudentResource;
use App\Services\AffiliatesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateController extends Controller
{
    protected $affiliatesService;
    public function __construct(AffiliatesService $affiliatesService) {
        $this->affiliatesService = $affiliatesService;
    }
    //
    public function index(Request $request)
    {
        $result['status'] = 200;
        $data = $request->validate([
            'searchValue' => 'sometimes|string|nullable',
            'start_date' => 'sometimes|date|nullable',
            'end_date' => 'sometimes|date|nullable',
        ]);
        $searchValue = $data['searchValue'] ?? null;
        $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
        $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
        $affiliates = $this->affiliatesService->all($searchValue,$start_date,$end_date);
        return response()->json([
            'data' => AffiliateResource::collection($affiliates),
            'current_page' => $affiliates->currentPage(),
            'total' => $affiliates->total(),
            'per_page' => $affiliates->perPage(),
            'last_page' => $affiliates->lastPage(),
            'status' => $result['status']
        ], 
        $result['status']);
    }

    public function getUserList(Request $request)
    {
        $result = ['status' => 200];
        $data = $request->validate([
            'searchValue' => 'sometimes|string|nullable',
            'start_date' => 'sometimes|date|nullable',
            'end_date' => 'sometimes|date|nullable',
        ]);
        $searchValue = $data['searchValue'] ?? null;
        $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
        $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
        try{
            return response()->json([
                'data' => $this->affiliatesService->getUserList($searchValue,$start_date,$end_date),
                'status' => 200
            ], $result['status']);
        }catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'data' => $e->getMessage()
            ],400);
        }
    }

    public function downloadAffiliateAssigneeList(Request $request)
    {
        $data = $request->validate([
            'searchValue' => 'sometimes|string|nullable',
            'start_date' => 'sometimes|date|nullable',
            'end_date' => 'sometimes|date|nullable',
        ]);
        $searchValue = $data['searchValue'] ?? null;
        $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
        $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
        
        return response()->json([
            'data' => $this->affiliatesService->downloadAffiliateAssigneeList($searchValue,$start_date,$end_date),
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        $result = ['status' => 200];
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'assigned_region' => 'sometimes|string|nullable',
            'commission_rate' => 'sometimes|numeric|nullable',
            'monthly_target' => 'sometimes|numeric|nullable',
            'full_name' => 'sometimes|string|nullable',
            'identification_no' => 'sometimes|string|nullable',
            'phone_number' => 'sometimes|string|nullable',
            'org_name' => 'sometimes|string|nullable',
            'country' => 'sometimes|string|nullable',
        ]);
        try{
            $data['affiliate_approved_at'] = now();
            $result['data'] = $this->affiliatesService->create($data);
        }catch(\Exception $e){
            $result['message'] = $e->getMessage();
            $result['status'] = 400;
        }
        return response()->json($result,$result['status']);
    }

    public function show($id)
    {
        $result = ['status' => 200];
        $result['data'] = new AffiliateResource($this->affiliatesService->find($id));
        return response()->json($result, $result['status']);
    }

    public function update(Request $request,int $id)
    {
        $result = ['status' => 200];
        $data = $request->validate([
            'assigned_region' => 'sometimes|string|nullable',
            'commission_rate' => 'sometimes|numeric|nullable',
            'monthly_target' => 'sometimes|numeric|nullable',
            'full_name' => 'sometimes|string|nullable',
            'identification_no' => 'sometimes|string|nullable',
            'phone_number' => 'sometimes|string|nullable',
            'org_name' => 'sometimes|string|nullable',
            'country' => 'sometimes|string|nullable',
        ]);
        try{
            $result['data'] = $this->affiliatesService->update($data,$id);
            return response()->json($result, $result['status']);
        }catch(\Exception $e){
            $result['message'] = $e->getMessage();
            $result['status'] = 400;
        }
        return response()->json($result,$result['status']);
    }


    public function destroy($id)
    {
        $result = ['status' => 200];
        try{
            $result['data'] = $this->affiliatesService->delete($id);
            return response()->json($result, $result['status']);
        }catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'data' => $e->getMessage()
            ],400);
        }
    }

    public function getStudentList(Request $request)
    {
        $result = ['status' => 200];
        $data = $request->validate([
            'searchValue' => 'sometimes|string|nullable',
            'start_date' => 'sometimes|date|nullable',
            'end_date' => 'sometimes|date|nullable',
        ]);
        $searchValue = $data['searchValue'] ?? null;
        $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
        $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
        try{
            $students = $this->affiliatesService->getStudentList($searchValue,$start_date,$end_date);
            return response()->json([
                'data' => ReferStudentResource::collection($students),
                'current_page' => $students->currentPage(),
                'total' => $students->total(),
                'per_page' => $students->perPage(),
                'last_page' => $students->lastPage(),
                'status' => $result['status']
            ], $result['status']);
        }catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'data' => $e->getMessage()
            ],400);
        }
    }

    // public function filterStudentList($email = null,$name = null)
    // {
    //     $result = ['status' => 200];
    //     try{
    //         return response()->json([
    //             'data' => ReferStudentResource::collection($this->affiliatesService->filterStudentList($email,$name)),
    //             'status' => 200
    //         ], $result['status']);
    //     }catch(\Exception $e){
    //         return response()->json([
    //             'status' => 400,
    //             'data' => $e->getMessage()
    //         ],400);
    //     }
    // }

    public function searchAffiliate(Request $request)
    {
        $result = ['status' => 200];
        $data = $request->validate([
            'searchValue' => 'required|string',
        ]);
        try{
            return response()->json([
                'data' => $this->affiliatesService->searchAffiliate($data['searchValue']),
                'status' => 200
            ], $result['status']);
        }catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'data' => $e->getMessage()
            ],400);
        }
    }

    public function applyAffiliate(Request $request)
    {
        $result = ['status' => 201];
        try{
                $data = $request->validate([
                    'full_name' => 'required|string|max:255',
                    'NRIC_number' => 'required|string|max:255|unique:affiliates,NRIC_number,NULL,id,deleted_at,NULL',
                    'id_document' => 'required|mimes:jpg,jpeg,png,svg,pdf|max:5120',
                    'country' => 'required|string|max:255',
                    'phone_number' => 'required|string|max:20|unique:affiliates,phone_number,NULL,id,deleted_at,NULL',
                    'org_name' => 'sometimes|string|max:255',
                    'gender' => 'required|in:male,female',
                    'custom_student_id' =>'sometimes|string|exists:students,identification_number',
                ]);
    
                $result['data'] = $this->affiliatesService->saveAffiliateApplication($data);

            return response()->json([
               'status' => 201,
               'message' => 'Affiliate application sent successfully'
            ], $result['status']);
        }catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'data' => $e->getMessage()
            ],400);
        }
    }

    public function getAffiliateApplications(Request $request)
    {
        $result = ['status' => 200];
        $data = $request->validate([
            'searchValue' => 'sometimes|string|nullable',
            'start_date' => 'sometimes|date|nullable',
            'end_date' => 'sometimes|date|nullable',
        ]);
        $searchValue = $data['searchValue'] ?? null;
        $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
        $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
        try{
            return response()->json([
                'data' => AffiliateApplicationResource::collection($this->affiliatesService->getAffiliateApplications($searchValue,$start_date,$end_date)),
                'current_page' => $this->affiliatesService->getAffiliateApplications()->currentPage(),
                'total' => $this->affiliatesService->getAffiliateApplications()->total(),
                'per_page' => $this->affiliatesService->getAffiliateApplications()->perPage(),
                'last_page' => $this->affiliatesService->getAffiliateApplications()->lastPage(),
               'status' => 200
            ], $result['status']);
        }catch(\Exception $e){
            return response()->json([
               'status' => 400,
                'data' => $e->getMessage()
            ],400);
        }
    }

    public function getAffiliateApplicationsRejectList()
    {
        $result = ['status' => 200];
        try{
            return response()->json([
                'data' => AffiliateApplicationResource::collection($this->affiliatesService->getAffiliateApplicationsRejectList()),
               'status' => 200
            ], $result['status']);
        }catch(\Exception $e){
            return response()->json([
               'status' => 400,
                'data' => $e->getMessage()
            ],400);
        }
    }

    public function confirmationAffiliateApplication(Request $request, int $id)
    {
        $result = ['status' => 200];
        try{
            $data = $request->validate([
               'status' =>'required|boolean',
            ]);
            $affiliates = $this->affiliatesService->confirmationAffiliateApplication($id,$data['status'] ? true : false);

            return response()->json([
               'status' => 200,
               'message' => $data['status'] ? 'Affiliate application approved successfully' : 'Affiliate application rejected successfully'
            ], $result['status']);
        }catch(\Exception $e){
            return response()->json([
               'status' => 400,
                'data' => $e->getMessage()
            ],400);
        }
    }

}
