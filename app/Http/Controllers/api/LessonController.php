<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Repositories\LessonRepository;
use App\Services\LessonService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\URL;
use function App\Helpers\storeFile;

class LessonController extends Controller
{
    protected $lessonService;

    public function __construct(LessonService $lessonService) {
        $this->lessonService = $lessonService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try{
            return response()->json([
                'data' => $this->lessonService->getAll(),
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json(['data' => $e->getMessage(),'status' => '400'],400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            //code...

            $data = $request->validate([
                'module_id' => 'required|integer',
                'title' => 'string|max:255',
                'contents' => 'string|nullable',
                'status' => 'in:draft,published',
                'video' => 'mimes:mpeg,mp4|max:102400|nullable',
                'video_thumbnail' => 'image:jpg,png,svg|max:10000|nullable',
                'video_description' => 'sometimes|string|nullable'
            ]);
            return response()->json([
                'data' => $this->lessonService->createLessons($request->all()),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            //
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
            return response()->json([
                'data' => $this->lessonService->getById($id),
                'status' => 200
            ],200);
        } catch (\Throwable $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 200
            ],200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        //
        try {
            //code...
            $data = $request->validate([
                'module_id' => 'sometimes|integer',
                'title' => 'string|max:255',
                'contents' => 'sometimes|string|nullable',
                'status' => 'in:draft,published',
                'video' => 'sometimes|mimetypes:video/avi,video/mpeg,video/mp4|max:100000|nullable',
                'video_thumbnail' => 'image:jpg,png,svg|max:10000|nullable',
                'video_description' => 'sometimes|string|nullable',
                'sorting_order' => 'required|integer',
            ]);

            return response()->json([
                'data' => $this->lessonService->updateLesson($data,$id),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            //
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
            return response()->json([
                'data' => $this->lessonService->deleteLesson($id),
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
     * Get lessons by module
     */
    public function getByModule(Request $request)
    {
        try{
            $data = $request->validate([
                'module_id' => 'required|integer'
            ]);
            return response()->json([
                'data' => $this->lessonService->getByModule($data['module_id']),
                'status' => 200
            ],200);
            
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    /**
     * Summary of uploadContentImage
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function uploadContentImage(Request $request){
        try{
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $path = storeFile($request->file('image'),"lesson/content_images/".date('Y-m-d')."");
            return response()->json([
                'data' => [
                    'original_name' => $path['originalName'],
                    'file_name' => $path['fileName'],
                    'url' => $path['url'],
                ],
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function updateSortingOrder(Request $request)
    {
        try{
            $data = $request->validate([
                'lessons' => 'required',
                'module_id' => 'required|integer'
            ]);

            return response()->json([
                'data' => $this->lessonService->updateSortingOrder($data),
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }
}
