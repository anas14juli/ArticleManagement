<?php

namespace App\Repository\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use App\Models\student_task;
use App\Repository\StudentTaskRepositoryInterface;

class studentTaskRepository extends BaseRepository implements StudentTaskRepositoryInterface
{

    public function __construct(student_task $model)
    {
        parent::__construct($model);
    }

    public function showalltasks()
    {
        return student_task::all();
    }

 
    public function FinishTask($model)
    {
       return $model->update();
    }
    

}
