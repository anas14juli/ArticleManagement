<?php

namespace App\Repository\Eloquent;

use App\Models\patient_request;
use Illuminate\Database\Eloquent\Collection;
use App\Repository\PatientRequestRepositoryInterface;

class patientRequestRepository extends BaseRepository implements PatientRequestRepositoryInterface
{

    public function __construct(patient_request $model)
    {
        parent::__construct($model);
    }


}
