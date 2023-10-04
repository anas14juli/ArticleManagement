<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Http\Requests\PatientRequest;
use App\Http\Requests\PatientRequestRequest;
use App\Policies\PatientRequestPolicy;
use App\Repository\Eloquent\patientRepository;
use App\Repository\Eloquent\patientRequestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PatientRequestService extends BaseController
{
    private $patientRequestRepository;
    private $patientRepository;
    public function __construct(patientRequestRepository $patientRequestRepository,patientRepository $patientRepository)
    {
        $this->patientRequestRepository = $patientRequestRepository;
        $this->patientRepository = $patientRepository;
    }


    public function ConsultationRequest(Request $request)
    {
        $user = auth('sanctum')->user();
        $patient=$this->patientRepository->where('user_id',$user->id);
        $validator = Validator::make($request->all(), [
            'phoneNumber' => 'required|unique:patient_requests,phoneNumber|digits_between:8,12',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }
        $Consultation=$this->patientRequestRepository->create([
            'phoneNumber'=>$request->phoneNumber,
            'type'=>'consultation',
            'description'=>$request->description,
            'title'=>'Null',
            'patient_id'=>$patient->id,
            'is_done'=>0
        ]);
        return $Consultation;
    }
}
