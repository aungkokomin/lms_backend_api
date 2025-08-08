<?php

namespace App\Http\Controllers\api\User;

use App\Exports\StudentFilterExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentGrantResource;
use App\Http\Resources\StudentResource;
use App\Services\StudentService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{

    protected $userService;
    protected $studentService;
    protected $role;

    public function __construct(UserService $userService, StudentService $studentService) {
        $this->userService = $userService;
        $this->studentService = $studentService;
        $this->role = 'student';
    }

    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //
        $data = $request->validate([
            'searchValue' => 'sometimes|string|nullable',
            'start_date' => 'sometimes|date|nullable',
            'end_date' => 'sometimes|date|nullable',
        ]);
        $searchValue = $data['searchValue'] ?? null;
        $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
        $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
        try {
            return response()->json([
                'data' => $this->studentService->studentList($searchValue,$start_date,$end_date),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    /**
     * Summary of search
     * @param Request $request 
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function downloadExcel(Request $request)
    {
        //
        try {
            $data = $request->validate([
                'searchValue' => 'sometimes|string|nullable',
                'start_date' => 'sometimes|date|nullable',
                'end_date' => 'sometimes|date|nullable',
                'format' => 'sometimes|string|in:csv,xlsx,xls,pdf',
            ]);

            $format = $data['format'] ?? 'csv';
            $searchValue = $data['searchValue'] ?? null;
            $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
            $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
            return response()->json([
                'data' => $this->studentService->downloadExcel($searchValue,$start_date,$end_date,$format),
                'status' => 200
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{
            $result = ['status' => 200];
            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                // 'NRIC_number' => 'required|string|max:255|unique:students,NRIC_number,NULL,id,deleted_at,NULL',
                'NRIC_number' => 'required|string|max:255',
                'id_document' => 'required|file|mimes:jpg,jpeg,png,svg,pdf|max:5120',
                'nationality' => 'required|string|max:255',
                'date_of_birth' => 'required|string|before:today',
                'address' => 'required|string|max:255',
                'zip_code' => 'string|max:255',
                'email' => 'required|string|email|max:255|exists:users',
                // 'phone_number' => 'required|string|max:20|unique:students,phone_number,NULL,id,deleted_at,NULL',
                'phone_number' => 'required|string|max:20',
                'city' => 'required|string|max:255',
                'gender' => 'required|boolean',
                'education' => 'sometimes|json',
                'edu_doc.*' => 'sometimes|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
                'grant_apply' => 'sometimes|boolean',
                'referrer_id' => 'sometimes|string|exists:users,referral_id',
            ]);
            // $data = $request->all();
            $data['role'] = $this->role;

            $result['data'] = $this->studentService->createStudent($data);
            return response()->json($result,$result['status']);
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $result['status'] = 200;
            $student = $this->studentService->show($id);

            return response()->json([
                'data' => new StudentResource($student),
                'status' => $result['status']
            ],$result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            $result = ['status' => 200];
            $data = $request->all();
            $data['role'] = $this->role;
            $result['data'] = $this->studentService->update($data, $id);
            return response()->json($result,$result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $result = ['status' => 200];
            $result['data'] = $this->studentService->delete($id);
            return response()->json($result,$result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function grantList(Request $request)
    {
        //
        try {
            $data = $request->validate([
                'searchValue' => 'sometimes|string|nullable',
                'start_date' => 'sometimes|date|nullable',
                'end_date' => 'sometimes|date|nullable',
            ]);
            $searchValue = $data['searchValue'] ?? null;
            $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
            $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
            $result = ['status' => 200];
            $student = $this->studentService->grantApplyList($searchValue,$start_date,$end_date);
            $result['data'] = StudentGrantResource::collection($student);
            return response()->json([
                'data' => $result['data'],
                'currentPage' => $student->currentPage(),
                'lastPage' => $student->lastPage(),
                'perPage' => $student->perPage(),
                'total' => $student->total(),
            ],$result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function grantHistory(Request $request)
    {
        //
        try {
            $data = $request->validate([
                'searchValue' => 'sometimes|string|nullable',
                'start_date' => 'sometimes|date|nullable',
                'end_date' => 'sometimes|date|nullable',
            ]);
            $searchValue = $data['searchValue'] ?? null;
            $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
            $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
            $result = ['status' => 200];
            $student = $this->studentService->grantHistoryList($searchValue,$start_date,$end_date);
            $result['data'] = StudentGrantResource::collection($student);
            return response()->json([
                'data' => $result['data'],
                'currentPage' => $student->currentPage(),
                'lastPage' => $student->lastPage(),
                'perPage' => $student->perPage(),
                'total' => $student->total(),
            ],$result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function grantConfirmation(Request $request)
    {
        //
        try {
            $data = $request->validate([
                'student_id' => 'required|exists:students,id',
                'is_approve' => 'required|boolean',
            ]);
            $result = ['status' => 200];
            $result['data'] = new StudentGrantResource($this->studentService->grantConfirmation($data));
            return response()->json($result,$result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }
}
