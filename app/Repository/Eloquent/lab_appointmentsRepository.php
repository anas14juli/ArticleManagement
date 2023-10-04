<?php

namespace App\Repository\Eloquent;

use App\Models\lab_appointment;
use App\Models\patient;
use Illuminate\Database\Eloquent\Collection;
use App\Models\student;
use App\Repository\lab_appointmentsRepositoryInterface;


class lab_appointmentsRepository extends BaseRepository implements lab_appointmentsRepositoryInterface
{

    public function __construct(lab_appointment $model)
    {
        parent::__construct($model);
    }

}
