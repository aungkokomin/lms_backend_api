<?php

namespace App\Repositories;

use App\Interfaces\ModuleRepositoryInterface;
use App\Models\Module;

class ModuleRepository implements ModuleRepositoryInterface
{
    protected $module;

    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    public function getAllModules($searchValue = null)
    {
        return $this->module->with('lessons','image','course')
        ->whereHas('course',function($query){
            return $query->where('deleted_at',NULL);
        })
        ->when($searchValue,function($query) use ($searchValue){
            return $query->where('title','like','%'.$searchValue.'%');
        })
        ->orderBy('module_order','asc')->paginate(100);
    }

    public function getModuleById($id)
    {
        return $this->module->with([
            'lessons' => function($query){
                return $query->orderBy('sorting_order','asc');
            },'image','lessons.quiz'
        ])->findOrFail($id);
    }

    public function getByCourse($courseId)
    {
        return $this->module->with([
            'lessons' => function($query){
                return $query->orderBy('sorting_order','asc');
            },'image'
        ])->where('course_id',$courseId)->orderBy('module_order','asc')->get();
    }

    public function createModule(array $data)
    {
        return $this->module->create($data);
    }

    public function updateModule($id, array $data)
    {
        $module = $this->module->findOrFail($id);
        $module->update($data);
        return $module->fresh();
    }

    public function deleteModule($id)
    {
        $module = $this->module->findOrFail($id);
        return $module->delete();
    }
}
