<?php

namespace App\Repository\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Prescription;
use App\Repository\PrescriptionRepositoryInterface;

class PrescriptionRepository extends BaseRepository implements PrescriptionRepositoryInterface
{

    public function __construct(Prescription $model)
    {
        parent::__construct($model);
    }

    public function ShowAllPrescriptions()
    {
        return prescription::all();
    }
    
}