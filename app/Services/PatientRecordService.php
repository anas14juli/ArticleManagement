<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Http\Requests\PatientRequest;
use App\Repository\Eloquent\patientProfileRepository;
use App\Repository\Eloquent\patientRecordRepository;
use App\Repository\Eloquent\patientRepository;
use Illuminate\Http\Request;


class PatientRecordService extends BaseController
{
    private $PatientRecordService;
    public function __construct(patientRecordRepository $PatientRecordService)
    {
        $this->PatientRecordService = $PatientRecordService;
    }

    public function ShowRecord($id)
    {
        $Record=$this->PatientRecordService->patient_record_find($id);
        return $Record;
    }
}
