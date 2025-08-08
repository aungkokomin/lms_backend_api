<?php
namespace App\Helpers;

use App\Interfaces\StudentRepositoryInterface;
use function App\Helpers\storeFile;

class EducationAdditionHelper
{
    protected $studentRepositoryInterface;

    public function __construct(StudentRepositoryInterface $studentRepositoryInterface)
    {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
    }

    public function addEducationInfo($data)
    {
        if (isset($data['education'])) {
            $education = json_decode($data['education'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid education data');
            }

            $i = 0;
            foreach ($education as $edu) {
                $edu['student_id'] = $data['student_id'];
                $result = $this->studentRepositoryInterface->addEducation($edu);
                if (isset($data['edu_doc'][$i]) && !empty($data['edu_doc'][$i])) {
                    $paths = storeFile($data['edu_doc'][$i], 'student/education/docs');
                    $result->document()->create([
                        'url' => $paths['url']
                    ]);
                }

                $i++;
            }
        }
        return true;
    }
}