<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Models\lab_appointment;
use App\Models\patient_record;
use App\Models\student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Gate;

class labManagerService extends BaseController
{
    public function ViewAvailableAppointments()
    {
        return lab_appointment::where('is_available', true)->get();
    }

    public function AddAvailableAppointment(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'daysAvailable' => 'required',
            'duration' => 'required',
        ]);

        $daysAvailable = $request->input('daysAvailable');

        // Check if $daysAvailable is a string
        if (is_string($daysAvailable)) {
            $daysAvailable = explode(',', $daysAvailable); // Convert the comma-separated string to an array
        }

        $startDate = Carbon::now();
        $endDate = Carbon::now()->addWeeks(2);

        $availableDates = [];

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            if (!$date->isFriday() && !$date->isSaturday()) {
                foreach ($daysAvailable as $dayAvailable) {
                    if ($date->{"is$dayAvailable"}()) {
                        $availableDates[] = $date->format('Y-m-d');
                    }
                }
            }
        }

        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $duration = $request->duration;

        // Calculate the number of appointments over the start and end period and duration
        $numAppointments = $startTime->diffInMinutes($endTime) / $duration;

        $labAppointments = [];

        foreach ($availableDates as $currentDate) {
            $startTimeCopy = $startTime->copy();
            for ($i = 0; $i < $numAppointments; $i++) {
                if ($i == 0) {
                    $start = $startTimeCopy;
                    $end = $start->copy()->addMinutes($duration);
                } else {
                    $start = $startTimeCopy->addMinutes($duration);
                    $end = $start->copy()->addMinutes($duration);
                }
                $labAppointment = lab_appointment::create([
                    'date' => $currentDate,
                    'start_time' => $start,
                    'end_time' => $end,
                ]);
                $labAppointments[] = $labAppointment;
            }
        }

        return $labAppointments;
    }

    public function bookAppointment(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        $student = student::where('user_id', $user->id)->first();
        $appointment = lab_appointment::findOrFail($id);
        $appointment->is_available = false;
        $appointment->patientName = $request->patientName;
        $appointment->student_id = $student->id;
        $appointment->save();
        return  $appointment;
    }

    public function viewBookedAppointments()
    {
        return lab_appointment::with('Students')->where('is_available', false)->get();
    }

    public function SendPhotoToStudent(Request $request, $id)
    {
        $appointment = lab_appointment::find($id);
        $patient_record = patient_record::where('patient_id', $appointment->patient_id)
            ->first();

        if (!$appointment || !$patient_record) {
            return response()->json(['message' =>
            'Appointment or patient record not found'], 404);
        }

        $file = Cloudinary::upload($request->file('photo')->getRealPath(), [
            'folder' => 'anas29December'
        ])->getSecurePath();

        $appointment->photo = $file;
        $appointment->save();

        $patient_record->photo = $file;
        $patient_record->save();

        return "Sended photo done";
    }
}
