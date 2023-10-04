<?php

namespace App\Repository\Eloquent;

use App\Models\patient;
use App\Models\patient_profile;
use App\Models\patient_record;
use Illuminate\Database\Eloquent\Collection;
use App\Models\student;
use App\Repository\patientProfileRepositoryInterface;
use App\Repository\patientRecordRepositoryInterface;
use App\Repository\PatientRepositoryInterface;

class patientRecordRepository extends BaseRepository implements patientRecordRepositoryInterface
{

    public function __construct(patient_record $model)
    {
        parent::__construct($model);
    }

    public function patient_record_find($id)
    {
        return $this->model->with('Patients','Students')->find($id);
    }
}
