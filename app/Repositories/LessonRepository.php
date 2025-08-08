<?php

namespace App\Repositories;

use App\Http\Resources\LessonResource;
use App\Interfaces\LessonRepositoryInterface;
use App\Models\Lesson;

class LessonRepository implements LessonRepositoryInterface
{
    // Add your repository methods here
    protected $lesson;
    
    public function __construct(Lesson $lesson) {
        $this->lesson = $lesson;
    }

    public function getAll()
    {
        $lessons = $this->lesson->with('video','quiz')->paginate(100);
        return LessonResource::collection($lessons); 
        // $lessons->map(function($lesson){
        //     $content = json_decode($lesson->contents);
        //     if(!is_null($content)){
        //         $lesson->contents = $content; 
        //     }
        // });
    }

    public function createLesson(array $data)
    {
        try{
            $last_sort_order = $this->lesson->where('module_id',$data['module_id'])->max('sorting_order');

            $lesson = $this->lesson->create([
                'module_id' => $data['module_id'],
                'title' => isset($data['title']) ? $data['title'] : NULL,
                'contents' => isset($data['contents']) ? json_encode($data['contents']) : NULL,
                'status' => isset($data['status']) ? $data['status'] : 'draft',
                'video_id' => isset($data['video_id']) ? $data['video_id'] : NULL,
                'sorting_order' => $last_sort_order + 1
            ]);
            return new LessonResource($lesson);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function updateLesson($id, array $data)
    {
        $lesson = $this->lesson->findOrFail($id);
        
        return $lesson->update($data);
        // [
        //     'module_id' => isset($data['module_id']) ? $data['module_id'] : $lesson->module_id,
        //     'title' => isset($data['title']) ? $data['title'] : $lesson->title,
        //     'contents' => isset($data['contents']) ? $data['contents'] : $lesson->contents,
        //     'status' => isset($data['status']) ? $data['status'] : $lesson->status,
        //     'video_id' => isset($data['video_id']) ? $data['video_id'] : NULL 
        // ]);
    }

    public function deleteLesson($id)
    {
        $lesson = $this->lesson->findOrFail($id);
        if($lesson->video()->exists()){
            // video file and thumbnail file should be deleted
            $video = $lesson->video;
            $video->thumbnail()->delete();
            $video->delete();
        }
        return $lesson->delete(); 
    }

    public function getLesson($id)
    {
        $lesson = $this->lesson->with('video','quiz')->findOrFail($id);
        return new LessonResource($lesson);
    }

    public function getByModule($moduleId)
    {
        $lessons = $this->lesson->where('module_id',$moduleId)->with('video','quiz')->orderBy('sorting_order','asc')->get();
        return LessonResource::collection($lessons); 
    }

    public function syncLessonSorting(array $data)
    {
        return $this->lesson->where('id',$data['id'])
        ->where('module_id',$data['module_id'])
        ->update([
            'sorting_order' => $data['sorting_order']
        ]);
    }
}