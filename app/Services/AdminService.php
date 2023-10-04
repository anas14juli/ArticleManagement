<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Models\calendar;
use App\Models\disease;
use App\Models\notification as notification1;
use App\Models\notification as ModelsNotification;
use App\Models\patient;
use App\Models\patient_profile;
use App\Models\patient_request;
use App\Models\student;
use App\Models\student_profile;
use App\Models\student_request;
use App\Models\User;
use App\Notifications\AdminNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;


class AdminService extends BaseController
{
    public function getAllPatientRequests()
    {
        return patient_request::with('Patients', 'diseases')->where('type', 'request')->get();
    }

    public function getAllStudentRequests()
    {
        return student_request::with('Students', 'diseases')->get();
    }


    public function getAllPatientConsultations()
    {
        return patient_request::with('Patients', 'diseases')->where('type', 'consultation')->get();
    }


    public function matching()
    {
        $student_request_model = new student_request();
        $getStudentRequests = $student_request_model->getStudentRequests();
        $patient_request_model = new patient_request();
        $student_model = new student();
        // $studentsWhoDoneMatchingWithAPatient[] = 0;
        $startTime = time();
        while (time() - $startTime < 3) {
            for ($i = 0; $i < $getStudentRequests->count(); $i++) {
                $StudentRequest = $getStudentRequests[$i];

                $getPatientRequests = $patient_request_model->getPatientRequestsThatRequestIsTypeAndIsDoneIsZero();

                if ($StudentRequest->number == null) {
                    $ThePatientwhoSubmittedTheFirstRequest = $patient_request_model->ThePatientwhoSubmittedTheFirstRequest($StudentRequest->disease_name_id);
                    if ($ThePatientwhoSubmittedTheFirstRequest == null)
                        continue;
                    if ($StudentRequest->disease_name_id == $ThePatientwhoSubmittedTheFirstRequest->disease_name_id) {
                        calendar::create([
                            'patient_id' => $ThePatientwhoSubmittedTheFirstRequest->patient_id,
                            'student_id' => $StudentRequest->student_id,
                            'disease_name' => $StudentRequest->disease_name_id,
                            'type' => 'request'
                        ]);
                        student_request::find($StudentRequest->id)->update([
                            'is_done' => 1
                        ]);
                        $ThePatientwhoSubmittedTheFirstRequest->is_done = 1;
                        $ThePatientwhoSubmittedTheFirstRequest->save();
                    }
                } else {
                    for ($j = 0; $j < $getPatientRequests->count(); $j++) {
                        $patientRequest = $getPatientRequests[$j];
                        if ($StudentRequest->disease_name_id == $patientRequest->disease_name_id) {
                            $studentRequestHavingBigestNumber = $student_request_model->HavingBigestNumber($StudentRequest->disease_name_id);
                            if ($studentRequestHavingBigestNumber->id != $StudentRequest->id) {
                                break;
                            }
                            calendar::create([
                                'patient_id' => $patientRequest->patient_id,
                                'student_id' => $StudentRequest->student_id,
                                'disease_name' => $patientRequest->disease_name_id,
                                'type' => 'request'
                            ]);
                            $patientRequest->is_done = 1;
                            $patientRequest->save();

                            $StudentRequest->decrement('number');

                            if ($StudentRequest->number == 0) {
                                $StudentRequest->is_done = 1;
                                $StudentRequest->save();
                                // $studentRequest->delete();
                                break;
                            }
                            break;
                        }
                    }
                }
                $studentsWhoDoneMatchingWithAPatient[] = $student_model->studentsWhoDoneMatchingWithAPatient($StudentRequest->student_id);
            }
        }
        $ids = collect(array_unique($studentsWhoDoneMatchingWithAPatient));
        $studentsWhoDoneMatchingWithAPatientUniqe = $student_model
            ->GetUsersFromUserIdInStudentModel($ids);
        $theUsersToWhomeTheNotificationWillBeSent = [];
        foreach ($studentsWhoDoneMatchingWithAPatientUniqe as $studentWhoDoneMatchingWithAPatientUniqe) {
            $theUsersToWhomeTheNotificationWillBeSent[] = $studentWhoDoneMatchingWithAPatientUniqe;
        }

        foreach ($theUsersToWhomeTheNotificationWillBeSent as $theUserToWhomeTheNotificationWillBeSent) {
            Notification::send(
                null,
                new AdminNotification(
                    'Hi ' . $theUserToWhomeTheNotificationWillBeSent->name . ',',
                    'A patient has been found for you to treat, go and check your patients',
                    [$theUserToWhomeTheNotificationWillBeSent->fcm_token]
                )
            );
        }
        return "Matching Process Is Done";
        /*
        send notification to The student
        */
    }


    public function matchingWithMasterStudent()
    {
        $patientRequests = patient_request::where('type', 'consultation')->where('is_done', 0)->get();
        $student_model = new student();
        for ($i = 0; $i < $patientRequests->count(); $i++) {
            $moreMasterStudentConsultationNumber = student::where('type', 'Master_Degree')
                ->orderByRaw("CAST(numberOfConsultations AS UNSIGNED) ASC")
                ->first();
            $theNumber = $moreMasterStudentConsultationNumber->numberOfConsultations;
            $Patient = patient::find($patientRequests[$i]->patient_id);
            student::find($moreMasterStudentConsultationNumber->id)->update([
                'numberOfConsultations' => $theNumber + 1
            ]);
            patient_request::find($patientRequests[$i]->id)->update([
                'is_done' => 1
            ]);
            calendar::create([
                'patient_id' => $Patient->id,
                'student_id' => $moreMasterStudentConsultationNumber->id,
                'type' => 'consultation',
            ]);
            $MasterStudentWhoDoneMatchingWithAPatientConsulation = $student_model->MasterStudentWhoDoneMatchingWithAPatientConsulation($moreMasterStudentConsultationNumber->user_id);
            notification1::create([
                'sender_id'=>244,
                'reciver_id'=>$moreMasterStudentConsultationNumber->user_id,
                'text'=>'There is a patient who wants a consultation, go check your consultations and make an appointment for him please'
            ]);
            Notification::send(
                null,
                new AdminNotification(
                    'Hi ' . $MasterStudentWhoDoneMatchingWithAPatientConsulation->name . ',',
                    'There is a patient who wants a consultation, go check your consultations and make an appointment for him please',
                    [$MasterStudentWhoDoneMatchingWithAPatientConsulation->fcm_token]
                )
            );
        }

        /*
        send notification to The master student
        */
        return "Matching Process Is Done";
    }

    public function deletePatient($id)
    {
        $patient = patient::find($id);
        $user = User::find($patient->user_id);
        $patientProfile = patient_profile::where('patient_id', $patient->id)->first();
        $patient->delete();
        $user->delete();
        $patientProfile->delete();
        return $this->sendResponse('true', 'deleted done');
    }

    public function deleteStudent($id)
    {
        $Student = Student::find($id);
        $user = User::find($Student->user_id);
        $StudentProfile = student_profile::where('student_id', $Student->id)->first();
        $Student->delete();
        $user->delete();
        $StudentProfile->delete();
        return $this->sendResponse('true', 'deleted done');
    }

    public function addClinicalCondition(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'year' => 'required',
            'Department' => 'required',
            'clinical_condition' => 'required',
        ]);
        return disease::create([
            'type' => $request->type,
            'specialization' => $request->specialization,
            'year' => $request->year,
            'Semester' => $request->Semester,
            'Department' => $request->Department,
            'subject' => $request->subject,
            'clinical_condition' => $request->clinical_condition,
            'number' => $request->number
        ]);
    }


    public function MatchingWithWhile()
    {

        $condition = true;
        $i = 0;
        $Checks = [];


        $CheckhasTrueValue = true;
        $diseaseNameIdSameBetweenTowTables = DB::table('patient_requests as pr1')
            ->join('student_requests as pr2', 'pr1.disease_name_id', '=', 'pr2.disease_name_id')
            ->select('pr1.disease_name_id', 'pr2.disease_name_id')
            ->distinct()
            ->get();

        for ($r = 0; $r < $diseaseNameIdSameBetweenTowTables->count(); $r++) {
            $TheValue = $diseaseNameIdSameBetweenTowTables[$r];
            $TheRows = student_request::where('disease_name_id', $TheValue->disease_name_id)->get();
            foreach ($TheRows as $TheRow) {
                if ($TheRow->number == null) {
                    $doesntExistInPatientRequests = patient_request::where('disease_name_id', $TheRow->disease_name_id)
                        ->doesntExist();
                    $Checks[] = $doesntExistInPatientRequests;
                } else {
                    $doesntExistInPatientRequests = patient_request::where('disease_name_id', $TheRow->disease_name_id)
                        ->doesntExist();
                    if ($doesntExistInPatientRequests = true) {
                        if ($TheRow->number == 0) {
                            $Checks[] = false;
                        } else {
                            $Checks[] = true;
                        }
                    } else {
                        $Checks[] = false;
                    }
                }
            }
        }
        // return $Checks;
        $CheckhasTrueValue = in_array(true, $Checks);
        if ($CheckhasTrueValue === false) {
            $CheckhasTrueValue = false;
        }
    }


    //statistics
    public function totalMasterStudentsStatistics()
    {
        $total = student::where('type', 'Master_Degree')->count();
        return $this->sendResponse($total, 'This is the number of the Master Students');
    }

    public function totalBachelorDegreeStudentsStatistics()
    {
        $total = student::where('type', 'Bachelor_Degree')->count();
        return $this->sendResponse($total, 'This is the number of the Normal Students');
    }

    public function totalPatientsStatistics()
    {
        return $this->sendResponse(patient::count(), 'This is the number of the Patient');
    }

    public function NameOfTheMostRatedStudent()
    {
        return $this->sendResponse(student_profile::orderByDesc('rating')->select('name')->first(), 'This is Name Of The Most Rating');
    }

    public function TheMostPatientWhoInteractsWithTheApplication()
    {
        $ThePatintNameAndNumberOfTreatments = [];
        $result = DB::table('calendars')
            ->select('patient_id', DB::raw('count(*) as Number_of_treatments'))
            ->groupBy('patient_id')
            ->orderBy('Number_of_treatments', 'desc')
            ->first();

        $theName = patient::find($result->patient_id)->name;
        $ThePatintNameAndNumberOfTreatments = [$theName, $result->Number_of_treatments];
        return $this->sendResponse($ThePatintNameAndNumberOfTreatments, 'This is The Patint Name And Number Of Treatments');
    }

    public function MoreStudentMasterWhoDoingConsulting()
    {
        $result = DB::table('students')
            ->select('name', 'numberOfConsultations')
            ->orderBy('numberOfConsultations', 'desc')
            ->limit(1)
            ->get();
        return $this->sendResponse($result, 'these are The More Students Master Who Doing Consulting');
    }

    public function TheClinicalConditionThatHasMoreTreatments()
    {
        $result = DB::table('calendars')
            ->where('type', 'request')
            ->select('disease_name', DB::raw('count(*) as Number_of_treatments'))
            ->groupBy('disease_name')
            ->orderBy('Number_of_treatments', 'desc')
            ->first();

            $theName=disease::where('id',$result->disease_name)->first();

        return $this->sendResponse($theName, 'This is The Clinical Condition That Has More Treatments');
    }
}
