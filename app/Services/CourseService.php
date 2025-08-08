<?php

namespace App\Services;

use App\Interfaces\CourseRepositoryInterface;
use App\Models\Module;
use Dflydev\DotAccessData\Exception\DataException;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Support\Facades\Validator;

use function App\Helpers\storeFile;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function getAllCourses()
    {
        return $this->courseRepository->getAllCourses();
    }

    public function getCourseById($id)
    {
        return $this->courseRepository->getCourseById($id);
    }

    public function createCourse(array $data)
    {
        try {
            $validator = Validator::make($data,[
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'thumbnail' => 'required|image|mimes:jpg,jpeg,png,svg|max:5120',
                'price' => 'required|numeric',
                'status' => 'required|in:draft,published',
            ]);
    
            if ($validator->fails()) {
                throw new ValidationException($validator->errors()->first());
            }
    
            if(isset($data['thumbnail'])){
                $paths = storeFile($data['thumbnail'],'course');
            }
    
            $course = $this->courseRepository->createCourse($data);

            if(isset($paths['url'])){
                $course->image()->create([
                    'url' => $paths['url']
                ]);
            }
            
            return $course;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateCourse($id, array $data)
    {
        try {
            $validator = Validator::make($data,[
                'title' => 'string|max:255',
                'description' => 'string|max:255',
                'thumbnail' => 'image|mimes:jpg,jpeg,png,svg|max:5120',
                'price' => 'numeric',
                'status' => 'in:draft,published',
            ]);
            if ($validator->fails()) {
                throw new ValidationException($validator->errors()->first());
            }

            if(isset($data['thumbnail'])){
                $paths = storeFile($data['thumbnail'],'course');
            }

            $course = $this->courseRepository->updateCourse($id, $data);
            
            if(isset($paths['url'])){
                if($course->image()->exists()){
                    $course->image()->update([
                        'url' => $paths['url']
                    ]);
                }else{
                    $course->image()->create([
                        'url' => $paths['url']
                    ]);
                }
            }
    
            return $course;
        } catch (\Exception $e) {
            throw new DataException($e->getMessage());
        }
    }

    public function deleteCourse($id)
    {
        return $this->courseRepository->deleteCourse($id);
    }

    public function getCourseByUserId($user_id)
    {
        return $this->courseRepository->getCourseByUserId($user_id);
    }

    public function updateCourseProgress(array $data) 
    {
        $validator = Validator::make($data,[
            'course_id' => 'required',
            'user_id' => 'required',
            'module_id' => 'required',
        ]);

        if($validator->fails()){
            throw new ValidationException($validator->errors()->first());
        }
        
        try{
            // get module progress data
            $moduleData = $this->getModuleDatas($data);
            $saveData = [
                'course_id' => $data['course_id'],
                'user_id' => $data['user_id'],
                'completed_modules' => $moduleData['completeModule'],
                'total_modules' => $moduleData['totalModule'],
                'progress_percentage' => $moduleData['progress_percentage']
            ];
            
            return $this->courseRepository->saveCourseProgress($saveData);

        }catch(Exception $e){
            throw new DataException($e->getMessage());
        }
    }

    /**
     * @param mixed $data
     * @return array
     */
    public function getModuleDatas($data){

        $module = Module::findOrFail($data['module_id']);
        $result['completeModule'] = ($module->module_order - 1) > 0 ? $module->module_order - 1 : 0;    
        $result['totalModule'] = Module::where('course_id',$data['course_id'])->count();
        $result['progress_percentage'] = ($result['completeModule'] / $result['totalModule']) * 100;

        return $result;
    }
}
