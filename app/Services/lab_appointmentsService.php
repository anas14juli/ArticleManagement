<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;

use App\Repository\Eloquent\studentRepository;
use App\Repository\Eloquent\lab_appointmentsRepository;
use App\Repository\Eloquent\patientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;
use App\Models\lab_appointment;
use App\Models\student;
use App\Models\patient;
use App\Models\lab_manager;


class lab_appointmentsService extends BaseController
{
   private $lab_appointmentsRepository;
   private $studentRepository;

   private $patientRepository;

    public function __construct(lab_appointmentsRepository $lab_appointmentsRepository,studentRepository $studentRepository)
    {
        $this->lab_appointmentsRepository = $lab_appointmentsRepository;
        $this->studentRepository = $studentRepository;

    }


    public function BookRoleInLab(Request $request)
    {
        $user = auth('sanctum')->user();
        $student=student::where('user_id',$user->id)->first();

        $bookrole=$this->lab_appointmentsRepository->create([
            'date'=>$request->date,
            'time'=>$request->time,
            'day'=>$request->day,
            'description'=>$request->description,
            'image'=>$request->image,
            'patient_name'=>$request->patient_name,
            'student_id'=>$student->id,
        ]);
        return $bookrole;
    }


}








