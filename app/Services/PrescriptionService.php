<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Repository\Eloquent\PrescriptionRepository;
use App\Repository\Eloquent\studentRepository;
use App\Repository\Eloquent\patientRepository;
use Illuminate\Http\Request;
use App\Models\student;
use App\Models\prescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class PrescriptionService extends BaseController
{
    private $prescriptionRepository;
    private $studentRepository;
    private $patientRepository;

    public function __construct(PrescriptionRepository $prescriptionRepository,studentRepository $studentRepository
    ,patientRepository $patientRepository)
    {
         $this->prescriptionRepository = $prescriptionRepository;
         $this->studentRepository=$studentRepository;
         $this->patientRepository=$patientRepository;
    }


    public function AddPrescription($id,Request $request)
    {
        $user=auth('sanctum')->user();
        $student =student::where('user_id',$user->id)->first();
       // $patient= $this->patientRepository->where('user_id',$user->id);
        $prescription= $this->prescriptionRepository->create([
           'disease_name'=>$request->disease_name,
           'description'=>$request->description,
           'starting_date'=>$request->starting_date,
           'end_date'=>$request->end_date,
           'patient_id'=>$id,
           'student_id'=>$student->id,

        ]);

        return $prescription;

    }

  /*  public function ShowAllPrescriptions()

    {
        $user=auth('sanctum')->user();
        $student =student::where('user_id',$user->id)->first();
        $myprescriptions=prescription::where('student_id', $student->id)->get();
        return $myprescriptions;
    }
    */

    public function ShowSinglePrescription($id)
    {
        $one_prescription=$this->prescriptionRepository->find($id);
        return $one_prescription;
    }
    public function DeletePrescription($id)
    {

            $prescription=$this->prescriptionRepository->find($id)->delete();
            return $prescription;

    }
}
