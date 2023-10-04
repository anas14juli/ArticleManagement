<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Models\calendar;
use App\Models\disease;
use App\Models\patient;
use App\Models\student;
use App\Models\patient_record;
use App\Models\patient_request;
use App\Models\student_request;
use App\Models\User;
use App\Repository\Eloquent\patientRepository;
use App\Repository\Eloquent\patientRequestRepository;
use App\Repository\Eloquent\studentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;


class StudentService extends BaseController
{
    private $studentRepository;
    private $patientRequestRepository;
    public function __construct(studentRepository $studentRepository, patientRequestRepository $patientRequestRepository,)
    {
        $this->studentRepository = $studentRepository;
        $this->patientRequestRepository = $patientRequestRepository;
    }

    public function getAllStudents()
    {
        $students = $this->studentRepository->getAllStudents();
        return $students;
    }

    public function patientTransfer(Request $request, $id)
    {
        $patientRequest = $this->patientRequestRepository->update($id, [
            'type' => 'request',
            'disease_name_id' => $request->disease_name_id,
            'description' => $request->description,
            'is_done'=>0
        ]);

        return $patientRequest;
    }

    public function getClinicalConditionsByDepartment($department)
    {
        return disease::where('Department',$department)->get();
    }

    public function mypatients()
    {
        $user = auth('sanctum')->user();
        $student = student::where('user_id', $user->id)->first();
        $patientsIds = calendar::where('student_id', $student->id)->where('type','request')->pluck('patient_id')->toArray();
        $uniquepatientsIds = collect(array_unique($patientsIds));
        return $mypatients = patient::whereIn('id', $uniquepatientsIds)->get();
    }

    public function myConsultations()
    {
        $user = auth('sanctum')->user();
        $student = student::where('user_id', $user->id)->first();
        $patientsIds = calendar::where('student_id', $student->id)->where('type','consultation')->pluck('patient_id')->toArray();
        $uniquepatientsIds = collect(array_unique($patientsIds));
        return $mypatients = patient_request::whereIn('patient_id', $uniquepatientsIds)->get();
    }

    public function addCalender($id, Request $request)
    {
        $user = auth('sanctum')->user();
        $student = student::where('user_id', $user->id)->first();
        $UserIdForThePatient=User::find(patient::find($id)->user_id);
        Notification::send(
            null,
            new AdminNotification(
                'Hi ' . $UserIdForThePatient->name . ',',
                'There is an appointment for you to treat or consultation, go check your appointments',
                [$UserIdForThePatient->fcm_token]
            )
        );
        return calendar::where('student_id', $student->id)->where('patient_id', $id)->update([
            'date' => $request->date,
            'time' => $request->time,
            'day' => $request->day,
        ]);
    }


    public function ShowAllCalendars()
    {
        $user=auth('sanctum')->user();
        $student =student::where('user_id',$user->id)->first();
        $mycalendars=calendar::where('student_id', $student->id)
            ->where('date','!=',null)
            ->where('time','!=',null)
            ->get();
        return $mycalendars;
    }


    public function ShowSingleCalendar($id)
    {
        $oneCalendar=calendar::find($id);
        return $oneCalendar;
    }

    public function DeleteFromCalendar($id)
    {
        $calendar=calendar::find($id)->delete();
        return $calendar;
    }

    public function SearchInCalendar($request)
    {
        $myCalendar=calendar::where('date','like','%'.$request.'%')->get();
        return $myCalendar;
    }



    public function CreatePatientRecord(Request $request,$id)
    {
        $user=auth('sanctum')->user();
        $student =student::where('user_id',$user->id)->first();
        $patientRecord= patient_record::create([
           'last_disease_name'=>$request->last_disease_name,
           'current_disease_name'=>$request->current_disease_name,
           'general_description'=>$request->general_description,
           'patient_id'=>$id,
           'student_id'=>$student->id,

        ]);

        return $patientRecord;
    }


    public function EditPatientRecord(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        $student = student::where('user_id', $user->id)->first();
        $patientRecord = patient_record::findOrFail($id);

        if ($patientRecord->student_id !== $student->id) {
            return response()->json(['error' => 'You are not authorized to edit this patient record.'], 403);
        }

        $patientRecord->update([
            'last_disease_name' => $request->last_disease_name,
            'current_disease_name' => $request->current_disease_name,
            'general_description' => $request->general_description
        ]);

        return $patientRecord;
    }

    public function ShowPatientRecord($id)
    {

            $Record=patient_record::where('patient_id',$id)->first();
            return $Record;

    }

    public function MakeARequest(Request $request)
    {
        $user=auth('sanctum')->user();
        $student =student::where('user_id',$user->id)->first();
         $disease=disease::find($request->disease_name_id);
        // return $disease->number;

        $studentrequest= student_request::create([
            'subject'=>$request->subject,
            'priority'=>$request->priority,
            'number'=>$disease->number,
            'disease_name_id'=>$request->disease_name_id,
            'student_id'=>$student->id,
            'year'=>$request->year,
            'specializations'=>$request->specializations
        ]);
        return $studentrequest;

    }

    public function ViewMyRequests()
    {
        $user = auth('sanctum')->user();
        $student=student::where('user_id',$user->id)->first();
        $student_requests=student_request::where('student_id', $student->id)->get();
        return $student_requests;
    }

     public function CancelRequest($id)
     {
        $user=auth('sanctum')->user();
        $student =student::where('user_id',$user->id)->first();
        $studentrequest= student_request::find($id)->where('is_done',0)->delete();

        return $studentrequest;
     }


    public function ShowClinicalConidition($request1,$request2)
    {
        $user=auth('sanctum')->user();
        $student =student::where('user_id',$user->id)->first();
        if($student->type=='Bachelor_Degree')
           {   $clinical_conditiotns= DB::table('diseases')->select('id','clinical_condition')
               ->where('year',$request1)
               ->where('subject',$request2)
               ->get();
               return $clinical_conditiotns;
           }
         elseif($student->type=='Master_Degree')
           {
              $clinical_conditiotn= DB::table('diseases')->select('id','clinical_condition')
               ->where('year',$request1)
               ->where('specialization',$request2)
                ->get();

                return $clinical_conditiotn;
           }

}
}
