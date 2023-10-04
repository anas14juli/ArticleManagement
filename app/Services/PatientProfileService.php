<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Http\Requests\PatientRequest;
use App\Repository\Eloquent\patientProfileRepository;
use App\Repository\Eloquent\patientRepository;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class PatientProfileService extends BaseController
{
    private $patientProfileRepository;
    public function __construct(patientProfileRepository $patientProfileRepository)
    {
        $this->patientProfileRepository = $patientProfileRepository;
    }


    public function UpdatePatientProfile(Request $request, $id)
    {
        if ($request->hasFile('photo')) {
            $myProfile = $this->patientProfileRepository->update($id, [
                $file = Cloudinary::upload($request->file('photo')->getRealPath(), [
                    'folder' => 'anas29December'
                ])->getSecurePath(),
                'phone' => $request->phone,
                'Region' => $request->Region,
                'photo' => $file,
            ]);
        }else{
            $myProfile = $this->patientProfileRepository->update($id, [
                'phone' => $request->phone,
                'Region' => $request->Region,
            ]);
        }

        return $myProfile;
    }

    public function ShowPatientProfile($id)
    {
        $profile=$this->patientProfileRepository->patient_profile_find($id);
        return $profile;
    }
}
