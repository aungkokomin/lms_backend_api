<?php

namespace App\Services;

use App\Http\Resources\LessonResource;
use App\Interfaces\ModuleRepositoryInterface;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function App\Helpers\storeFile;

class ModuleService
{
    protected $moduleRepository;

    public function __construct(ModuleRepositoryInterface $moduleRepository)
    {
        $this->moduleRepository = $moduleRepository;
    }

    public function getAllModules($searchValue = null)
    {
        return $this->moduleRepository->getAllModules($searchValue);
    }

    public function getModuleById($id)
    {
        $module = $this->moduleRepository->getModuleById($id);
        $user = User::findOrFail(Auth::id());
        $student_id = $user->student()->first() ? $user->student()->first()->id : null;
        
        $module->lessons->map(function($lesson) use ($user,$student_id){
            $progress = null;
            if($user->hasRole(Role::ROLE_STUDENT)){
                $progress = LessonProgress::where('lesson_id', $lesson->id)->where('student_id', $student_id)->first();
            }
            $lesson->is_completed = $progress ? $progress->completed : false;
            $lesson->is_locked = !$progress ? true : false;
        });
        return $module;
    }

    public function getByCourse($courseId)
    {
        $module = $this->moduleRepository->getByCourse($courseId);
        $user = Auth::user();
        $student_id = $user->student()->first() ? $user->student()->first()->id : null;
        
        $module->map(function($module) use ($user,$student_id){
            $module->lessons->map(function($lesson)use ($user,$student_id){
                $progress = null;
                if($user->hasRole(Role::ROLE_STUDENT)){
                    $progress = LessonProgress::where('lesson_id', $lesson->id)->where('student_id', $student_id)->first();
                }
                $lesson->is_completed = $progress ? $progress->completed : false;
                $lesson->is_locked = !$progress ? true : false;
            });
        });
        return $module; 
    }

    public function createModule(array $data)
    {
        $validator = Validator::make($data,[
            'course_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'descrption' => 'string',
            'status' => 'string|in:draft,published',
            'price' => 'numeric',
            'thumbnail' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'learn_duration' => 'sometimes|string|nullable|max:255',
        ]);

        if($validator->fails()){
            throw new ValidationException($validator->errors()->first());
        }
        if(!isset($data['module_order']) || is_null($data['module_order']) || $data['module_order'] < 0){
            $moduleCount = Module::where('course_id',$data['course_id'])->count();
            $data['module_order'] = $moduleCount + 1;
        }
        
        $result = $this->moduleRepository->createModule($data);
        $thumbnail = $data['thumbnail'] ?? null;
        if($result){
            if($thumbnail){
                $paths = storeFile($thumbnail,'modules/thumbnails');
                $result->image()->create([
                    'url' => $paths['url']
                ]);
            }
        }
        return $result->fresh();
    }

    public function updateModule($id, array $data)
    {
        $validator = Validator::make($data,[
            'course_id' => 'sometimes|integer',
            'title' => 'sometimes|string|max:255',
            'descrption' => 'string',
            'module_order' => 'integer',
            'status' => 'string|in:draft,published',
            'price' => 'numeric',
            'thumbnail' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'learn_duration' => 'sometimes|string|nullable|max:255',
        ]);

        if($validator->fails()){
            throw new ValidationException($validator->errors()->first());
        }

        $data = $validator->validated();

        // module order must be round number greater than 0
        if(!isset($data['module_order']) || is_null($data['module_order']) || $data['module_order'] <= 0){
            unset($data['module_order']);
        }
        
        $result = $this->moduleRepository->updateModule($id, $data);

        $thumbnail = $data['thumbnail'] ?? null;
        if($result){
            if($thumbnail){
                $paths = storeFile($thumbnail,'modules/thumbnails');
                if($result->image()->exists()){
                    $result->image()->update(['url' => $paths['url']]);
                }else{
                    $result->image()->create(['url' => $paths['url']]);
                }
                $result->image = $result->image()->first();
            }
        }

        return $result; 
    }

    public function deleteModule($id)
    {
        return $this->moduleRepository->deleteModule($id);
    }

    public function getByShowCaseCourse($courseId)
    {
        $module = $this->moduleRepository->getByCourse($courseId);
        return $module; 
    }
}
