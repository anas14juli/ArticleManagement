<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Repository\Eloquent\studentTaskRepository;
use App\Repository\Eloquent\studentRepository;
use Illuminate\Http\Request;
use App\Models\student_task;
use App\Models\student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class StudentTaskService extends BaseController
{
    private $studentTaskRepository;
    private $studentRepository;

    public function __construct(studentTaskRepository $studentTaskRepository,studentRepository $studentRepository)
    {
        $this->studentTaskRepository = $studentTaskRepository;
        $this->studentRepository = $studentRepository;
    }


    public function AddTask(Request $request)
    {

        $user = auth('sanctum')->user();
        $student=student::where('user_id',$user->id)->first();
        $student_task=$this->studentTaskRepository->create([
            'task_name'=>$request->task_name,
            'description'=>$request->description,
            'starting_date'=>$request->starting_date,
            'end_date'=>$request->end_date,
            'number'=>$request->number,
            'task_status'=>$request->task_status,
            'student_id'=>$student->id,
        ]);
        return $student_task;
    }

    public function DeleteTask($id)
    {

            $task=$this->studentTaskRepository->find($id)->delete();
            return $task;

    }

    public function UpdateTask(Request $request,$id)
    {
        $user = auth('sanctum')->user();
        $student=student::where('user_id',$user->id)->first();
        $student_task=$this->studentTaskRepository->find($id)->update([
            'task_name'=>$request->task_name,
            'description'=>$request->description,
            'starting_date'=>$request->starting_date,
            'end_date'=>$request->end_date,
            'number'=>$request->number,
            'task_status'=>$request->task_status,
            'student_id'=>$student->id,
        ]);
        return $student_task;
    }

public function FinishTask(Request $request,$id)
    {
        $user = auth('sanctum')->user();
        $student_task=$this->studentTaskRepository->find($id)->update([
            'task_status'=>$request->task_status,
        ]);
        return $student_task;
    }



    public function ShowSingleTask($id)
    {
        $one_task=$this->studentTaskRepository->find($id);
        return $one_task;
    }

    public function showalltasks()
    {
        $user = auth('sanctum')->user();
        $student=student::where('user_id',$user->id)->first();
        $student_task=student_task::where('student_id', $student->id)->get();
        return $student_task;
    }


}
