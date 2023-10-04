<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Http\Requests\PatientRequest;
use App\Repository\Eloquent\patientProfileRepository;
use App\Repository\Eloquent\patientRepository;
use App\Repository\Eloquent\studentProfileRepository;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class StudentProfileService extends BaseController
{
    private $studentProfileRepository;
    public function __construct(studentProfileRepository $studentProfileRepository)
    {
        $this->studentProfileRepository = $studentProfileRepository;
    }


    public function UpdateStudentProfile(Request $request, $id)
    {
        // $fileName = time() . '.' . $request->photo->extension();
        // $request->photo->move(public_path('uploads'), $fileName);
        $myProfile = $this->studentProfileRepository->update($id, [
            $file = Cloudinary::upload($request->file('photo')->getRealPath(), [
                'folder' => 'anas29December'
            ])->getSecurePath(),
            'phone' => $request->phone,
            'Region' => $request->Region,
            'description' => $request->description,
            'photo' => $file,
        ]);

        return $myProfile;
    }

    public function ShowStudentProfile($id)
    {
        $profile = $this->studentProfileRepository->student_profile_find($id);
        return $profile;
    }
}
