<?php

namespace App\Services;

use App\Interfaces\LessonRepositoryInterface;
use App\Models\Video;
use Dflydev\DotAccessData\Exception\DataException;
use Illuminate\Support\Facades\Validator;

use function App\Helpers\storeFile;

class LessonService
{
    protected $lessonRepositoryInterface;

    public function __construct(LessonRepositoryInterface $lessonRepositoryInterface) {
        $this->lessonRepositoryInterface = $lessonRepositoryInterface;
    }
    // Add your repository methods here
    public function getAll()
    {
        $lessons = $this->lessonRepositoryInterface->getAll();
        
        return $lessons;
    }

    public function getById($id)
    {
        return $this->lessonRepositoryInterface->getLesson($id);
    }

    public function getByModule(int $module_id)
    {
        return $this->lessonRepositoryInterface->getByModule($module_id);
    }

    /**
     * Summary of createLessons
     * @param array $data
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return mixed
     */
    public function createLessons(array $data)
    {
        try{
            if(isset($data['video'])){
                $paths = storeFile($data['video'],'lesson/videos');
                
                $video = Video::create([
                    'title' => $paths['originalName'],
                    'description' => $data['video_description'],
                    'video_url' => $paths['url'],
                    'status' => 'published'
                ]);
                
                $data['video_id'] = $video->id;
                
                
                logger('Video model created successfully');
            
            }else{
                    
                logger('No video in request data found.');
            }
            
            if(isset($video) && isset($data['video_thumbnail'])){
                $paths = storeFile($data['video_thumbnail'],'lesson/thumbnails');
                $video->thumbnail()->create([
                    'url' => $paths['url']
                ]);

                logger('Video thumbnail created successfully');
            
            }else{

                logger('video Create failed or No video thumbnail in request data found.');
                        
            }

            $lesson = $this->lessonRepositoryInterface->createLesson($data);
            
            logger('Lesson created successfully');
            
            return $lesson;
            
        }catch(\Exception $e){
            throw new DataException($e->getMessage());
        }
    }


    /**
     * Summary of updateLesson
     * @param mixed $data
     * @param mixed $id
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return mixed
     */
    public function updateLesson($data,$id)
    {
        try{
            $lesson = $this->lessonRepositoryInterface->getLesson($id);
            if(isset($data['video'])){
                $paths = storeFile($data['video'],'lesson/videos');
                $video = Video::create([
                    'title' => $paths['originalName'],
                    'description' => $data['video_description'],
                    'video_url' => $paths['url'],
                    'status' => 'published'
                ]);
                $data['video_id'] = $video->id;
                
                logger('Video model created successfully');
                
                if($video && isset($data['video_thumbnail'])){
                    $paths = storeFile($data['video_thumbnail'],'lesson/thumbnails');
                
                    logger('Video Thumbnail Store Processes Completed');
                
                }else{
                
                    logger('No video thumbnail in request data found.');
                
                }

                logger('Video Files Store Processes Completed');

            } else {
                
                logger('No video in request data found.');
                
                if(isset($data['video_thumbnail']) || isset($data['video_description'])){
                    $video = $lesson->video;
                    if($video){
                        if(isset($data['video_thumbnail'])){
                            $paths = storeFile($data['video_thumbnail'],'lesson/thumbnails');
                        }else{
                            $paths['url'] = $video->thumbnail()->first()->url;
                        }
                        
                        $video->update([
                            'description' => isset($data['video_description']) ? $data['video_description'] : $video->description,
                            'thumbnail_url' => isset($paths['url']) ? $paths['url'] : $video->thumbnail_url
                        ]);
                    }
                }
            }

            if(isset($video)){
                if($video->thumbnail()->exists()){
                    $video->thumbnail()->update([
                        'url' => $paths['url']
                    ]);
                
                    logger('Video thumbnail updated successfully');
                
                }else{
                    $video->thumbnail()->create([
                        'url' => $paths['url']
                    ]);
                
                    logger('Video thumbnail created successfully');
                
                }
                
                $data['video_id'] = $video->id;
            }

            $lesson = $this->lessonRepositoryInterface->updateLesson($id,$data);
            
            logger('Lesson updated successfully');
            
            return $lesson;
        }catch(\Exception $e){
            throw new DataException($e->getMessage());
        }
    }

    public function deleteLesson($id)
    {
        return $this->lessonRepositoryInterface->deleteLesson($id);
    }

    public function updateSortingOrder(array $data)
    {
        try {
            $sorted_ids = json_decode($data['lessons'])->sortedLessonIds;

            if(json_last_error() !== JSON_ERROR_NONE){
                throw new DataException('Invalid JSON data');
            }

            for($i = 0; $i < count($sorted_ids); $i++){
                $this->lessonRepositoryInterface->syncLessonSorting([
                    'id' => $sorted_ids[$i],
                    'module_id' => $data['module_id'],
                    'sorting_order' => $i + 1
                ]);
            }
            
            return $this->lessonRepositoryInterface->getByModule($data['module_id']);
        } catch (\Throwable $e) {
            throw new DataException($e->getMessage());
        }
    }
}