<?php

namespace App\Repository\Eloquent;

use App\Models\patient;
use App\Models\patient_profile;
use Illuminate\Database\Eloquent\Collection;
use App\Models\student;
use App\Repository\patientProfileRepositoryInterface;
use App\Repository\PatientRepositoryInterface;

class patientProfileRepository extends BaseRepository implements patientProfileRepositoryInterface
{

    public function __construct(patient_profile $model)
    {
        parent::__construct($model);
    }

    public function patient_profile_find($id)
    {
        return $this->model->with('Patients')->find($id);
    }


}
