<?php

namespace App\Repository\Eloquent;

use App\Models\patient;
use Illuminate\Database\Eloquent\Collection;
use App\Models\student;
use App\Repository\PatientRepositoryInterface;

class patientRepository extends BaseRepository implements PatientRepositoryInterface
{

    public function __construct(patient $model)
    {
        parent::__construct($model);
    }

    public function where($x,$y)
    {
        return patient::where($x,$y)->first();
    }

    public function getAllPatients()
    {
        return patient::all();
    }
    public function deletePatient($model)
    {
        return $model->delete();
    }
}
