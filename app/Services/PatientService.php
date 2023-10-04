<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Http\Requests\PatientRequest;
use App\Models\calendar;
use App\Models\prescription;
use App\Models\student;
use App\Models\student_discount;
use App\Models\student_profile;
use App\Models\student_rating;
use App\Repository\Eloquent\patientRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;


class PatientService extends BaseController
{
    private $patientRepository;
    public function __construct(patientRepository $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    public function getAllPatients()
    {
        $patients = $this->patientRepository->getAllPatients();
        return $patients;
    }

    public function ConsultationRequest(Request $request)
    {
        $data = $request->all();
        $Consultation = $this->patientRepository->create([$data]);
        return $Consultation;
    }

    public function cancelAppointment($id)
    {
        $appointment = calendar::find($id);

        if (!$appointment) {
            return $this->sendError('error', 'The appointment is not exists');
        }

        $now = Carbon::now();
        $appointmentDate = Carbon::parse($appointment->date); //Appointment date between the patient and student
        $diffInHours = $now->diffInHours($appointmentDate); //The difference in hours between the current time and the appointment

        if ($diffInHours >= 48) {
            $appointment->delete();
            return $this->sendResponse('success', 'The appointment has been canceled');
        } else {
            return $this->sendError('error', 'The appointment cannot be canceled before 48 hours');
        }
    }

    public function myAppointment()
    {
        $user = auth('sanctum')->user();
        $patient = $this->patientRepository->where('user_id', $user->id);
        $appointments = calendar::with('Patients', 'Students')->where('patient_id', $patient->id)->get();
        if (!$appointments) {
            return $this->sendError('error', 'you do not have appointments');
        }
        return $this->sendResponse($appointments, 'These are your appointments');
    }

    public function addRating(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        $patient = $this->patientRepository->where('user_id', $user->id);

        $rating = student_rating::where('patient_id', $patient->id)->create([
            'value' => $request->value,
            'patient_id' => $patient->id,
            'student_id' => $id
        ]);

        $ratingCount = student_rating::count(); //number of rating to the one Student
        $ratings = student_rating::where('student_id', $id)->get();
        $ratingSum = 0; //sum of ratings to the one student

        foreach ($ratings as $rating) {
            $ratingSum = $ratingSum + $rating->value;
        }
        $finalRating = ($ratingSum) / ($ratingCount);

        student_profile::where('student_id', $id)->update([
            'rating' => $finalRating,
        ]);
        $topFiveProfilesHavingRating = student_profile::orderByDesc('rating')
            ->take(5)->get();
        $i = 0;
        student_discount::truncate();
        foreach ($topFiveProfilesHavingRating as $oneOfTheTop) {
            $StudentId = $oneOfTheTop->student_id;
            $i += 5;
            $value = 30 - $i;
            $student_discount = new student_discount();
            $student_discount->student_id = $StudentId;
            $student_discount->value = $value;
            $student_discount->save();
        }
        return $this->sendResponse($rating, 'Rating added successfully');
    }

    public function myPrescriptions()
    {
        $user = auth('sanctum')->user();
        $patient = $this->patientRepository->where('user_id', $user->id);

        return prescription::where('patient_id', $patient->id)->get();
    }

    public function myOnePrescription($id)
    {
        return prescription::find($id);
    }

    public function FindOutMyStudentID()
    {
        $user = auth('sanctum')->user();
        $patient = $this->patientRepository->where('user_id', $user->id);
        return calendar::where('type','request')->where('patient_id', $patient->id)->with('Students')->first();
    }
}
