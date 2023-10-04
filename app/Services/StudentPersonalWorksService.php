<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Models\personal_work;
use App\Models\student;
use App\Repository\Eloquent\studentRepository;
use App\Repository\Eloquent\StudentPersonalWorksRepository;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;


class StudentPersonalWorksService extends BaseController
{
    private $studentRepository;
    private $studentPersonalWorksRepository;

    public function __construct(studentRepository $studentRepository,StudentPersonalWorksRepository $studentPersonalWorksRepository)
    {
        $this->studentRepository = $studentRepository;
       $this->studentPersonalWorksRepository =$studentPersonalWorksRepository;
    }

/*
   public function UploadImage(Request $request){
        $file = Cloudinary::upload($request->file('photo')->getRealPath(),[
            'folder'=>'PersonalWorks'
        ])->getSecurePath();
        dd($file);
        }
*/

    public function AddToMyPersonalWorks(Request $request)
    {

        $user = auth('sanctum')->user();
        $student = student::where('user_id', $user->id)->first();

     //   $file = time() . '.' . $request->photo->extension();
     //   $request->photo->move(public_path('uploads'), $file);

        $Myworks= personal_work::create([
            'name'=>$request->name,
            'description'=>$request->description,
          $file=Cloudinary::upload($request->file('photo')->getRealPath(),[
            'folder'=>'PersonalWorks'
           ])->getSecurePath(),
            'photo'=>$file,
            'subject_name'=>$request->subject_name,
            'student_id'=>$student->id,

         ]);

         return $Myworks;
    }

    public function DeleteFromMyWorks($id)
    {
        $MyWorks=$this->studentPersonalWorksRepository->find($id)->delete();
        return $MyWorks;
    }

    public function ShowMyWorks()
    {
        $user = auth('sanctum')->user();
        $student=student::where('user_id',$user->id)->first();
        $student_works=personal_work::where('student_id', $student->id)->get();
        return $student_works;
    }

    public function SearchForMyWorks($request)
    {

        $Works=personal_work::where('name','like','%'.$request.'%')->get();
        return $Works;
    }



}

