<?php

namespace App\Repository;

interface PatientRepositoryInterface extends EloquentRepositoryInterface
{
    public function getAllPatients();
    public function deletePatient($model);
}
