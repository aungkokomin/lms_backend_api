<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use function App\Helpers\generateCustomStudentId;

class StudentIdentificationNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $students = Student::get();
        foreach($students as $student){
            if(!$student->identification_number){
                $student->identification_number = generateCustomStudentId($student->id);
                $student->save();
                echo($student->identification_number.'+'.$student->id.'/');
            }
        }
    }
}
