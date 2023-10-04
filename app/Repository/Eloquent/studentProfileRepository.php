<?php

namespace App\Repository\Eloquent;

use App\Models\patient;
use App\Models\patient_profile;
use Illuminate\Database\Eloquent\Collection;
use App\Models\student;
use App\Models\student_profile;
use App\Repository\patientProfileRepositoryInterface;
use App\Repository\PatientRepositoryInterface;
use App\Repository\studentProfileRepositoryInterface;

class studentProfileRepository extends BaseRepository implements studentProfileRepositoryInterface
{

    public function __construct(student_profile $model)
    {
        parent::__construct($model);
    }

    public function student_profile_find($id)
    {
        return $this->model->with('Students')->find($id);
    }
}
