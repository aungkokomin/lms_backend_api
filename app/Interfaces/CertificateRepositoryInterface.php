<?php

namespace App\Interfaces;

interface CertificateRepositoryInterface
{
    // Add your Interfaces methods here

    public function get();

    public function create(array $data);

    public function show(int $id);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function getCertificateByCourse(int $course_id);

    public function getCertificateByModule(int $module_id);

    public function studentCertificateList($searchValue = NULL, $start_date = NULL, $end_date = NULL,$status = NULL);

    public function getCertificateByStudent(int $student_id);
}