<?php

namespace App\Repositories;

use App\Interfaces\InstructorRepositoryInterface;
use App\Models\Instructor;
use App\Models\User;

class InstructorRepository implements InstructorRepositoryInterface
{
    // Add your repository methods here
    protected $instructor;

    public function __construct(Instructor $instructor) {
        $this->instructor = $instructor;
    }

    public function list()
    {
        return $this->instructor->with('user')->paginate(10);
    }

    public function create($data)
    {
        if(isset($data['email'])){
            $user = User::where('email',$data['email'])->firstOrFail();
        }

        $instructor = $this->instructor->create([
            
        ]);
    }
    
    public function show($id)
    {
        return $this->instructor->with('user')->findOrFail($id);
    }
}