<?php

namespace App\Interfaces;

interface AffiliatesRepositoryInterface
{
    // Add your Interfaces methods here
    public function get($searchValue = NULL, $start_date = NULL, $end_date = NULL);

    public function getUserList($searchValue = NULL, $start_date = NULL, $end_date = NULL);

    public function searchUserList(string $searchValue);

    public function show(int $id);

    public function create(array $data);

    public function update(array $data,int $id);

    public function delete(int $id);

    public function getStudentList($referral_id, $searchValue = NULL, $start_date = NULL, $end_date = NULL);

    // public function filterStudentList($referral_id,$email,$name);

    public function searchAffiliates($searchValue);

    public function getAffiliateApplications($searchValue, $start_date, $end_date);

    public function getAffiliateApplicationsRejectList($searchValue, $start_date, $end_date);

    public function confirmationAffiliateApplication(int $id, bool $status);
}